<?php
/**
 * The Job model currently includes all the code for process management etc.; this should be moved
 * out at some point.
 */
class Job extends AppModel {
	var $name = 'Job';
	var $displayField = 'title';

    var $statusCodes = array ('pending', 'running', 'finished', 'error');


    /**
     * Attempts to get human readable status information about a job. Possible responses are:
     * - Pending (place in queue, time estimates)
     * - Running (latest output from status file/socket, % complete, time estimate)
     * - Finished (time to complete, link to report)
     * - Error (error info)
     *
     * @return array of info (probably to pass on to view)
     */
    function bgGetProgress () {
        $status = $this->field ('status');
        if ($status !== FALSE) {

            $rtn = array (
                'statusCode' => $status,
                'statusName' => $this->_bgGetStatusName($status),
            );

            switch ($status) {
                case 0: // Pending
                    $qp = $this->bgGetQueuePos();
                    $rtn['statusText'] = sprintf ("Your job is in position #%d in the queue", $qp);
                    break;
                case 1: // Running
                    $rtn['statusText'] = sprintf ("");
                    break;

                case 2: // Complete
                    $rtn['statusText'] = sprintf ("");
                    break;

                case 3: // Error
                    $rtn['statusText'] = sprintf ("");
                    break;

            }

        }
        else return false;
    }

    function _bgGetStatusName ($statusCode = -1) {
        return (isset ($this->statusCodes[$statusCode])) ?
                $this->statusCodes[$statusCode] :
                sprintf ('%1d ' . 'Unknown', $statusCode);
    }
    /**
     * How many jobs are ahead of this one for processing?
     * @return int number of jobs ahead of this job in the queue with status < 2 (i.e. jobs which are pending or running)
     */
    function bgGetQueuePos () {
        $id = $this->field ('id');
        if ($id !== FALSE) {
            return $this->find('count', array (
                'conditions' => array (
                    'Job.id >' => $this->field('id'),
                    'Job.status <' => 2,
                )
            ));
        }
        else return false;
    }
    function bg () {}
    /**
     * @param int $since unix timestamp for log cutoff
     * @return array statuses of the process is running, should be running, has crashed
     */
    function bgpGetStatus ($since = null) {
        $since = ($since === null) ? time () : $since;
        $status = $this->field ('status');
        if ($status !== FALSE) {

            $rtn = array (
                'statusCode' => $status,
                'statusName' => $this->_bgGetStatusName($status),
            );

            switch ($status) {
                case 0: // Pending
                    $qp = $this->bgGetQueuePos();
                    $rtn['statusText'] = sprintf ("Your job is in position #%d in the queue", $qp);
                    break;
                case 1: // Running
                    // Crash detection
                    if ($this->bgpBOYD()) {
                        // ruh roh
                        $rtn['statusText'] = sprintf ("Uh oh, it looks like the job has crashed. Stand by for a status update!");
                    }
                    else {
                        $rtn['statusText'] = sprintf ("Your job is currently being processed.");
                        $rtn['statusFile'] = bgpGetStatusFileSince ($since);

                    }
                    
                    break;

                case 2: // Complete
                    $rtn['statusText'] = sprintf ("");
                    break;

                case 3: // Error
                    $rtn['statusText'] = sprintf ("");
                    break;
            }
            
            return $rtn;
        }
        else {
            return false;
        }
    }
    /**
     *
     * @return mixed int pid if pidfile exists else null
     */
    function bgpGetPid () {
        $pid = $this->_bgpGetJobFileContent('pid');
        return ($pid == false) ? sprintf ("%d", $pid) : false;
    }
    /**
     * Reads status file a line at a time from the top and stops once the timestamp
     * @param int $since unix timestamp to look for lines stamped after
     * @return mixed string of status file since timestamp or false if none available/no file
     */
    function bgpGetStatusFileSince ($since = null) {
        $since = ($since === null) ? time () : $since;
        $fn = $this->bgpGetJobFileName ('status');
        if (!file_exists ($fn)) return false;
        $handle = fopen ($fn, 'r');
        $op = '';
        $old = false;
        while (!feof ($handle) && !$old) {
            $line = fgets ($handle, 4096);
            if (preg_match ("/^(\d+?:(\.\d}))\s/", $line, $m) > 0) {
                $ts = (float) $m[1];
                if ($ts > $since)
                    $op .= $line;
                else
                    $old = true;
            }
        }
        return (strlen ($op) == 0) ? false : $op;
    }
    /**
     *
     * @param string $file extension of file e.g. pid, status, log etc.
     * @return string
     */
    function bgpGetJobFileName ($file = 'pid') {
        $id = $this->field('id');
        if ($id !== FALSE) {
            $file = TMP . sprintf ("/jobrun/job_%d.%s", $id, $file);
        }
        return $file;
    }
    /**
     * Generic function for getting stuff out of one of a jobs possible files
     * @param <type> $file see bgpGetJobFile
     * @return string file content or false if doesn't exist
     */
    function _bgpGetJobFileContent ($file = 'pid') {
        $fn = $this->bgpGetJobFileName($file);
        return (file_exists ($fn)) ? file_get_contents ($fn) : false;
    }

    /**
     * See if a process is running given its pid (POSIX only)
     * @param int $pid
     * @return bool true if process with pid $pid is running
     */
    function bgpIsRunning ($pid = null) {
        $pid = ($pid == null) ? $this->bgpGetPid() : $pid;
        return file_exists (sprintf ("/proc/%d", $pid));
    }
    /**
     * Bring Out Your Dead
     * See if the process has died ungracefully and update plus return status if so
     * @return boolean has the process running this just exited without cleaning up?
     */
    function bgpBOYD () {
        if ($this->field ('status') == 1 && $this->bgpGetPid() !== false)
            return !$this->bgpIsRunning ();
        return false;
    }
    

	var $validate = array(
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'data' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'processor_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'pub_ref' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
?>