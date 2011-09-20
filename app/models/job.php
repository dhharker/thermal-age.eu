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
            $statusName = (isset ($this->statusCodes[$status])) ?
                    $this->statusCodes[$status] :
                    sprintf ('%1d ' . 'Unknown', $status);

            $rtn = array (
                'statusCode' => $status,
                'statusName' => $statusName,
            );

            switch ($status) {
                case 0: // Pending
                    $rtn['statusText'] = sprintf ("");
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
    function bgGetQueuePos () {
        $status = $this->field ('status');
        if ($status !== FALSE) {
            
        }
        else return false;
    }
    function bg () {}



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