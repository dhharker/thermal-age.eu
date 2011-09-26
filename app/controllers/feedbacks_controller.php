<?php
class FeedbacksController extends AppController {

	var $name = 'Feedbacks';

    function __construct () {
        parent::__construct ();

        $this->set ('title_for_layout', 'Send Feedback');

    }

	function index() {
		$this->Feedback->recursive = 0;
		$this->set('feedbacks', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid feedback', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('feedback', $this->Feedback->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Feedback->create();
            $this->Feedback->set ($this->data);
            $this->Feedback->set ('client_info', serialize (array (
                'server' => $_SERVER,
                'request' => $_REQUEST
            )));
            if ($this->Feedback->validates ()) {
                
                $this->Feedback->save ();
                App::import('View', 'Thanks');
                $this->render ('thanks');
            }
            else {

            }
            /*
			if ($this->Feedback->save($this->data)) {
				$this->Session->setFlash(__('The feedback has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The feedback could not be saved. Please, try again.', true));
			} */
		}
	}
    function thanks () {
        
    }
	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid feedback', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Feedback->save($this->data)) {
				$this->Session->setFlash(__('The feedback has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The feedback could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Feedback->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for feedback', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Feedback->delete($id)) {
			$this->Session->setFlash(__('Feedback deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Feedback was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>