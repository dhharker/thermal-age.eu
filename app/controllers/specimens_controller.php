<?php
class SpecimensController extends AppController {

	var $name = 'Specimens';

	function index() {
		$this->Specimen->recursive = 0;
		$this->set('specimens', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid specimen', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('specimen', $this->Specimen->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Specimen->create();
			if ($this->Specimen->save($this->data)) {
				$this->Session->setFlash(__('The specimen has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The specimen could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid specimen', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Specimen->save($this->data)) {
				$this->Session->setFlash(__('The specimen has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The specimen could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Specimen->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for specimen', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Specimen->delete($id)) {
			$this->Session->setFlash(__('Specimen deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Specimen was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>