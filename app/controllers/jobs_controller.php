<?php
class JobsController extends AppController {

	var $name = 'Jobs';
    
    function beforeFilter() {
        parent::beforeFilter();
        //$this->Auth->allow(array('report'));
    }
    
    function job_list ($list = null, $since = 0) {
        if (isset ($_GET['since']) && is_numeric ($_GET['since']) && $_GET['since'] > 0)
            $since = $_GET['since'];
        $list = (in_array ($list, array ('recent','incomplete'))) ? $list : null;
        
        header ('ax-new-epoch: ' . time());
        
        $user_id = $this->Auth->user('id');
        $jobSections = $this->Job->getSectionsByUserId ($list, $user_id, $since);
        $JSCs = $this->Job->statusCodes;
        $this->set(compact('jobSections','JSCs'));
    }
    
    
    /**
     * Users get bounced here when a job is finished (with or without errors)
     * @param int $id of job to get report for
     */
    function report ($id = null) {
        $j = $this->Job->read(null, $id);
        
        
        //debug ($this->view); die();
        if ($j !== false) {
            $status = $this->Job->bgpGetStatus ();
            $this->set ('status', $status);
            $fn = $this->Job->bgpGetJobFileName ('report');
            if (file_exists ($fn)) {
                $results = file_get_contents ($fn);
                $uns = @unserialize($results);
                if ($uns !== false)
                    $results = $uns;
            }
            else
                $results = "Couldn't find report, sorry!";
            $this->set ('results', $results);
            $this->set ('job', $j);
        }
        else {
            $this->set ('results', "Error :-(");
        }

        $this->render ($j['Job']['reporter_name'] . '_report');

    }

    /**
     * Users are redirected here after submitting a job for processing.
     * @param int $id of job to get status of
     */
    function status ($id = null) {

        $this->disableCache();

        $j = $this->Job->read(array (
            'Job.id',
            'Job.title',
            'Job.status',
            'Job.created',
        ), $id);

        $skey = 'jobstatus' . $j['Job']['id'] . 'since';
        if ($this->Session->check ($skey)) {
            $since = $this->Session->read ($skey);
        }
        else {
            $since = time ();
        }
        $this->Session->write ($skey, time());

        $this->set ('job', $j);
        $this->set ('status', $this->Job->bgpGetStatus ());//$since)); ignoring this for now as status update page currently too simple for it
        $async = $this->RequestHandler->isAjax ();
        $this->set ('async', $async);

        if ($j['Job']['status'] >= 2) // if job is complete, with or without error
            $this->redirect(array('action' => 'report', $id));
        elseif ($j['Job']['status'] == 0 && !$async) // job is pending
            $this->Job->tryProcessNext();
        elseif ($j['Job']['status'] == 1) // job is running
            $this->Job->bgpBOYD(); // check if *this job* has crashed
    }


	function index() {
		$this->Job->recursive = 0;
		$this->set('jobs', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid job', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('job', $this->Job->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Job->create();
			if ($this->Job->save($this->data)) {
				$this->Session->setFlash(__('The job has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The job could not be saved. Please, try again.', true));
			}
		}
		$users = $this->Job->User->find('list');
		$this->set(compact('users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid job', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Job->save($this->data)) {
				$this->Session->setFlash(__('The job has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The job could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Job->read(null, $id);
		}
		$users = $this->Job->User->find('list');
		$this->set(compact('users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for job', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Job->delete($id)) {
			$this->Session->setFlash(__('Job deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Job was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>