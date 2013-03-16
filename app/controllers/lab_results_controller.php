<?php
class LabResultsController extends AppController {

	var $name = 'LabResults';
	
    function add () {
        
    }
    
    
    function job ($job_id = null) {
        if (!$this->LabResult->Job->idExists($job_id))
            $this->redirect (array ('action' => 'index'));
            //$this->cakeError('error404');
        
        //$j = $this->LabResult->Job->read(null, $job_id);
        $user_id = $this->Auth->user('id');
        $lab_results = $this->LabResult->find('all', array (
            'conditions' => array (
                'LabResult.job_id' => $job_id,
                'LabResult.user_id' => $user_id,
                'Job.user_id' => $user_id
            )
        ));
        
        $this->set(compact('lab_results'));
        
        
    }
    
}
