<?php
class LayersController extends AppController {

	var $name = 'Layers';

	function index() {
		$this->Layer->recursive = 0;
		$this->set('layers', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid layer', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('layer', $this->Layer->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Layer->create();
			if ($this->Layer->save($this->data)) {
				$this->Session->setFlash(__('The layer has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The layer could not be saved. Please, try again.', true));
			}
		}
		$soils = $this->Layer->Soil->find('list');
		$this->set(compact('soils'));
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid layer', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Layer->save($this->data)) {
				$this->Session->setFlash(__('The layer has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The layer could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Layer->read(null, $id);
		}
		$soils = $this->Layer->Soil->find('list');
		$this->set(compact('soils'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for layer', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Layer->delete($id)) {
			$this->Session->setFlash(__('Layer deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Layer was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>