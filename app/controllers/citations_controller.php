<?php
class CitationsController extends AppController {

	var $name = 'Citations';

	function index() {
		$this->Citation->recursive = 0;
		$this->set('citations', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid citation', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('citation', $this->Citation->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Citation->create();
			if ($this->Citation->save($this->data)) {
				$this->Session->setFlash(__('The citation has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The citation could not be saved. Please, try again.', true));
			}
		}
		$users = $this->Citation->User->find('list');
		$this->set(compact('users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid citation', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Citation->save($this->data)) {
				$this->Session->setFlash(__('The citation has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The citation could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Citation->read(null, $id);
		}
		$users = $this->Citation->User->find('list');
		$this->set(compact('users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for citation', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Citation->delete($id)) {
			$this->Session->setFlash(__('Citation deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Citation was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>