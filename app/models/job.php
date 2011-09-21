<?php
/**
 * The Job model currently includes all the code for process management etc.; this should be moved
 * out at some point.
 */
class Job extends AppModel {
	var $name = 'Job';
	var $displayField = 'title';
    var $statusCodes = array ('pending', 'running', 'finished', 'error');

    var $maxThreads = 1; // maximum number of concurrent bg processors at a time

    /**
     * To be run from CLI. Finds the next job in the queue and runs it.
     */
    function tryProcessNext () {
        if (!$this->_goodToGo())
            return false;
        if (PHP_SAPI !== 'cli')
            return $this->_forkToBackground ();

        // init

        // load ttkpl

    }

    /**
     * Creates runtime files etc.
     * @return bool success
     */
    function _startProcessing () {
        $id = $this->field ('id');
        if ($id !== false) {

            App:import ('Vendor', 'ttkpl/predict.php');

            foreach (array ('pid', 'status') as $f)
                $this->bg[$f] = $this->bgpGetJobFileName();
            file_put_contents($this->bg['pid'], posix_getpid ());
            file_put_contents($this->bg['status'], '');
            $this->_addToStatus("Starting processor for job $id");
            $this->bg['startTime'] = microtime (true);
            return true;
        }
        return false;
    }
    /**
     * Cleans up runtime files
     */
    function _stopProcessing () {
        $this->bg['stopTime'] = microtime (true);
        unlink ($this->bg['pid']);
        $this->_addToStatus("Finished job $id");
        $this->_addToStatus("Total runtime was " . ($this->bg['stopTime'] - $this->bg['startTime']));
    }
    /**
     * Adds a timestamped message to the TOP of the status file
     * @return bool false if status file doesn't exist or can't be written else true
     */
    function _addToStatus ($message) {
        if (file_exists ($this->bg['status'])) {
            $st = file_get_contents ($this->bg['status']);
            $fmsg = sprintf("%f %s\n", microtime (true), $message);
            echo $fmsg;
            $st = $fmsg . $st;
            return file_put_contents ($this->bg['status'], $st);
        }
        return false;
    }
    /**
     * Spawns a background process to run tryProcessNext
     */
    function _forkToBackground () {
        $command = $command = CAKE . "console/cake \"--app\" \"" . APP . "\" background";
        $pid = shell_exec ("nohup $command 2> /dev/null & echo $!");
        return ($pid);
    }
    /**
     * Run by tryProcessNext prior to doing any work; initialises
     * @return bool is it ok to create another bg processing thread
     */
    function _goodToGo () {
        // if running threads < maxThreads return true
        return true;
    }

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
                    if ($qp < 1)
                        $rtn['statusText'] = "Your job is up next!";
                    else
                        $rtn['statusText'] = sprintf ("There %s %d job%s ahead of yours in the queue...", ($qp>1)?'are':'is', $qp, ($qp>1)?'s':'');
                    break;
                case 1: // Running
                    $rtn['statusText'] = sprintf ("Your job is running now.");
                    break;

                case 2: // Complete
                    $rtn['statusText'] = sprintf ("Job is complete.");
                    break;

                case 3: // Error
                    $rtn['statusText'] = sprintf ("Job finished, but with errors.");
                    break;
            }
            
            return $rtn;
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
                    'Job.id <' => $this->field('id'),
                    'Job.status <' => 2,
                )
            ));
        }
        else return false;
    }
    /**
     * @param int $since unix timestamp for log cutoff
     * @return array statuses of the process is running, should be running, has crashed
     */
    function bgpGetStatus ($since = null) {
        $since = ($since === null) ? time () : $since;
        $status = $this->field ('status');
        if ($status !== false) {

            $rtn = array (
                'statusCode' => $status,
                'statusName' => $this->_bgGetStatusName($status),
            );

            if ($status == 1) { // Status = Running
                // Crash detection
                if ($this->bgpBOYD()) {
                    // ruh roh
                    $rtn['statusText'] = sprintf ("Uh oh, it looks like the job has crashed. Stand by for a status update!");
                }
                else {
                    $rtn['statusText'] = sprintf ("Your job is currently being processed.");
                    $rtn['statusFile'] = bgpGetStatusFileSince ($since);

                }
                return $rtn;
            }
            else {
                return $this->bgGetProgress ();
            }
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