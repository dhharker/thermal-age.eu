<?php
class TemporothermalsController extends AppController {

	var $name = 'Temporothermals';

	function index() {
		$this->Temporothermal->recursive = 0;
		$this->set('temporothermals', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid temporothermal', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('temporothermal', $this->Temporothermal->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Temporothermal->create();
			if ($this->Temporothermal->save($this->data)) {
				$this->Session->setFlash(__('The temporothermal has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The temporothermal could not be saved. Please, try again.', true));
			}
		}
		$files = $this->Temporothermal->File->find('list');
		$users = $this->Temporothermal->User->find('list');
		$this->set(compact('files', 'users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid temporothermal', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Temporothermal->save($this->data)) {
				$this->Session->setFlash(__('The temporothermal has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The temporothermal could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Temporothermal->read(null, $id);
		}
		$files = $this->Temporothermal->File->find('list');
		$users = $this->Temporothermal->User->find('list');
		$this->set(compact('files', 'users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for temporothermal', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Temporothermal->delete($id)) {
			$this->Session->setFlash(__('Temporothermal deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Temporothermal was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>