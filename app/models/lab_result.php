<?php
class LabResult extends AppModel {
	var $name = 'LabResult';
	var $validate = array(
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				'allowEmpty' => true,
				'required' => true,
				//'last' => false, // Stop validation after this rule
				'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'job_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				'required' => true,
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
		),
		'Job' => array(
			'className' => 'Job',
			'foreignKey' => 'job_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    
    var $virtualFields = array (
        'pcr_percent' => '((LabResult.pcr_num_successes / LabResult.pcr_num_runs) * 100)'
    );
    
    
    function afterFind($results, $primary = false) {
        $results = parent::afterFind($results, $primary);
        /*foreach ($results as &$result)
            $this->_calculateLambdaFromExperimental($result);*/
        return $results;
    }
    
    function _calculateLambdaFromExperimental (&$data = null) {
        if ($data === null) $data =& $this->data;
        // calculate lambda from either pcr or htp data
        if (isset ($data['LabResult']['result_type']) && isset ($data['LabResult']) && $data['LabResult']['result_type'] == 'run') {
            if ($data['LabResult']['experiment_type'] == 'pcr') {
                $pl = $data['LabResult']['pcr_num_successes'] / $data['LabResult']['pcr_num_runs'];
                $l = $data['LabResult']['pcr_tgt_length'];
                $λ = 1.0 - pow ($pl, (1.0 / ($l - 1.0)));
            }
            elseif ($data['LabResult']['experiment_type'] == 'htp') {
                $ml = $data['LabResult']['htp_mfl_less_contaminants'];
                $λ = 1.0 / ($ml - 1.0);
            }
            $λ = round ($λ, 4);
            $data['LabResult']['lambda'] = $λ;
        }
    }
    
    function beforeSave() {
        parent::beforeSave();
        $this->_calculateLambdaFromExperimental();
        return true;
    }
}
