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
    function beforeSave() {
        $this->_calculateLambdaFromExperimental($this->data);
        //die (print_r ($this->data,1));
        return parent::beforeSave();
    }
    
    function _calculateLambdaFromExperimental (&$data = null) {
        if ($data === null) {
            $data =& $this->data;
            if (!isset ($data['LabResult']['id']))
                $data['LabResult']['id'] = $this->id;
        }
        if (is_array ($data['LabResult'])) {
            if (isset ($data['LabResult']['id'])) {
                // Edit operation
                if (!isset ($data['LabResult']['experiment_type']) || !isset ($data['LabResult']['result_type'])) {
                    $newData = $data;
                    $current = $this->find('first',array (
                        'conditions' => array (
                            'LabResult.id' => $data['LabResult']['id']
                        ),
                        'fields' => array (
                            'LabResult.experiment_type',
                            'LabResult.result_type'
                        )
                    ));
                    
                    if (is_array ($current)) {
                        $data = array_merge_recursive ($current, $newData);
                    }
                    //print_r (compact('newData','current','data'));die();
                }
            }
            else {
                // this is a new record
                //echo "no ids wtrf";
                //print_r ($data); die();
            }
            // calculate lambda from either pcr or htp data
            if( isset ($data['LabResult']['result_type']) && $data['LabResult']['result_type'] == 'run') {
                if ($data['LabResult']['experiment_type'] == 'pcr') {
                    $pl = $data['LabResult']['pcr_num_successes'] / $data['LabResult']['pcr_num_runs'];
                    
                    // a
                    //if ($pl == 1) // Treat "100% success" as if there was a 95% chance of getting this result
                      //  $pl = 1-(1/($data['LabResult']['pcr_num_runs']+1));// wild stab in the dark strategy which didn't work: treat it like the next one would have failed
                    
                    $l = $data['LabResult']['pcr_tgt_length'];
                    $λ = 1.0 - pow ($pl, (1.0 / ($l - 1.0)));
                }
                elseif ($data['LabResult']['experiment_type'] == 'htp') {
                    $ml = $data['LabResult']['htp_mfl_less_contaminants'];
                    $λ = ($ml < 2) ? 1 : $λ = 1.0 / ($ml - 1.0);
                }
                $λ = round ($λ, 8);
                $data['LabResult']['lambda'] = $λ;
                
                if (empty ($data['LabResult']['modelled_lambda'])) {
                    $jdat = unserialize ($this->Job->_bgpGetJobFileContent('report',$data['LabResult']['job_id']));
                    if ($jdat !== false && isset($jdat['summary']) && isset ($jdat['summary']['λ']))
                       $data['LabResult']['modelled_lambda'] = $jdat['summary']['λ'];
                }
            }
        }
        else {
            print_r (compact ('data')); die();
        }
    }
}
