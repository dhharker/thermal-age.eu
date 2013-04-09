<?php
class LabResultsController extends AppController {

	var $name = 'LabResults';
    
    function index() {
		$this->LabResult->recursive = 0;
		$this->set('labResults', $this->paginate('LabResult',array (
            'LabResult.user_id' => $this->Auth->user('id')
        )));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid labResults', true));
			$this->redirect(array('action' => 'index'), null, true, true);
		}
		$this->set('labResult', $this->LabResult->find('first',array (
            'conditions' => array (
                'LabResult.user_id' => $this->Auth->user('id'),
                'LabResult.id' => $id
            )
        )));
	}

	function add() {
		if (!empty($this->data)) {
            //print_r ($this->data);
            //die();
			$this->LabResult->create();
			$this->LabResult->set($this->data);
			if ($this->LabResult->validates() && $this->LabResult->save()) {
				$this->Session->setFlash(__('The lab results have been saved', true));
                $this->_redirectAfterDoingStuff($this->data['LabResult']['job_id']);
			} else {
				$this->Session->setFlash(__('The lab results could not be saved. Please, try again.', true));
			}
		}
        
		$this->_setStuffByJobId ($this->data['LabResult']['job_id']);
	}

	function edit($id = null, $job_id = null) {
        
        if ($job_id !== null) {
            $job_id = $job_id+0;
            //$this->set ('afterSuccess','job');
        }
        
        if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid lab results', true));
			$this->_redirectAfterDoingStuff($job_id);
		}
        elseif (!$id && !empty($this->data)) {
            $id = $this->data['LabResult']['id'];
            $job_id = $this->data['LabResult']['job_id'];
        }
        //print_r (compact ('id','job_id'));echo("AS");
		if (!empty($this->data) && $this->LabResult->set($this->data)) {
            //print_r (compact ('id','job_id'));die("AB");
			if ($this->LabResult->validates()) {
                $this->LabResult->_calculateLambdaFromExperimental ($this->data);
                $this->LabResult->save();
				$this->Session->setFlash(__('The lab results have been saved', true));
                //die ("jid is $job_id");
				$this->_redirectAfterDoingStuff($job_id);
			} else {
				$this->Session->setFlash(__('The lab results could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->LabResult->read(null, $id);
		}
        $this->set('editMode', true);
		$this->_setStuffByJobId ($this->LabResult->data['Job']['id']);
	}

	function delete($id = null, $job_id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for lab results', true));
			$this->_redirectAfterDoingStuff($job_id);
		}
		if ($this->LabResult->delete($id)) {
			$this->Session->setFlash(__('Lab result deleted', true));
			$this->_redirectAfterDoingStuff($job_id);
		}
		$this->Session->setFlash(__('Lab result was not deleted', true));
		$this->_redirectAfterDoingStuff($job_id);
	}
    
    
    function job ($job_id = null) {
        if ($job_id === null && isset ($this->data['LabResult']) && isset ($this->data['LabResult']['job_id']))
            $job_id = $this->data['LabResult']['job_id'];
        if (!empty ($this->data)) {
            $this->data['LabResult']['job_id'] = $job_id;
            $this->data['LabResult']['user_id'] = $this->Auth->user('id');
        }
        $this->_setStuffByJobId ($job_id);
        if (!empty($this->data)) {
            //print_r ($this->data);die();
			$this->LabResult->create();
			$this->LabResult->set($this->data);
			if ($this->LabResult->validates() && $this->LabResult->save()) {
				$this->Session->setFlash(__('The lab results have been saved', true));
                $this->redirect(array('action' => 'job', $this->data['LabResult']['job_id']), null, true, true);
			} else {
				$this->Session->setFlash(__('The lab results could not be saved. Please, try again.'.print_r ($this->LabResult->validationErrors,1), true));
			}
        }
        $authd = $this->authoriseWrite('Job',$job_id);
        if ($authd !== true) {
            $this->set('showForm', false);
        }
            
    }
    
    function _redirectAfterDoingStuff ($job_id = null) {
        if (!$job_id) die ("no jid $job_id");
        $jid = ($job_id === null && isset ($this->data['LabResult']['job_id'])) ? $this->data['LabResult']['job_id'] : $job_id;
        if ((isset ($this->data['LabResult']['after_success']) && 
            $this->data['LabResult']['after_success'] == 'job' && 
            isset ($this->data['LabResult']['job_id']) && 
            $this->data['LabResult']['job_id'] > 0) ||
            $job_id !== null)
        {
            $this->redirect(array('action' => 'job', $jid), null, true, true);
        }
        else
            $this->redirect(array('action' => 'index'), null, true, true);
    }
    function _setStuffByJobId ($job_id = null) {
        if (!$this->LabResult->Job->idExists($job_id))
            $this->_redirectAfterDoingStuff($job_id);
            //$this->redirect (array ('action' => 'index'));
            //$this->cakeError('error404');
        
        //$j = $this->LabResult->Job->read(null, $job_id);
        $user_id = $this->Auth->user('id');
        $labResults = $this->LabResult->find('all', array (
            'conditions' => array (
                'LabResult.job_id' => $job_id,
                'OR' => array (
                    'LabResult.user_id' => $user_id,
                    'LabResult.shared' => '1'
                )
            )
        ));
        $this->set(compact('labResults','job_id'));
    }
    
}
