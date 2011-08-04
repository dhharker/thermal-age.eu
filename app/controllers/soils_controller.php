<?php
class SoilsController extends AppController {

	var $name = 'Soils';

	function index() {
		$this->Soil->recursive = 0;
		$this->set('soils', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid soil', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('soil', $this->Soil->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Soil->create();
			if ($this->Soil->save($this->data)) {
				$this->Session->setFlash(__('The soil has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The soil could not be saved. Please, try again.', true));
			}
		}
		$citations = $this->Soil->Citation->find('list');
		$users = $this->Soil->User->find('list');
		$this->set(compact('citations', 'users'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid soil', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Soil->save($this->data)) {
				$this->Session->setFlash(__('The soil has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The soil could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Soil->read(null, $id);
		}
		$citations = $this->Soil->Citation->find('list');
		$users = $this->Soil->User->find('list');
		$this->set(compact('citations', 'users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for soil', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Soil->delete($id)) {
			$this->Session->setFlash(__('Soil deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Soil was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>