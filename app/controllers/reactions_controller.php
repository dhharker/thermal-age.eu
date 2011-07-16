<?php
class ReactionsController extends AppController {

	var $name = 'Reactions';

	function index() {
		$this->Reaction->recursive = 0;
		$this->set('reactions', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid reaction', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('reaction', $this->Reaction->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Reaction->create();
			if ($this->Reaction->save($this->data)) {
				$this->Session->setFlash(__('The reaction has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The reaction could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid reaction', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Reaction->save($this->data)) {
				$this->Session->setFlash(__('The reaction has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The reaction could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Reaction->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for reaction', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Reaction->delete($id)) {
			$this->Session->setFlash(__('Reaction deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Reaction was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
	function thermal-age.localhost/admin/istrator_index() {
		$this->Reaction->recursive = 0;
		$this->set('reactions', $this->paginate());
	}

	function thermal-age.localhost/admin/istrator_view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid reaction', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('reaction', $this->Reaction->read(null, $id));
	}

	function thermal-age.localhost/admin/istrator_add() {
		if (!empty($this->data)) {
			$this->Reaction->create();
			if ($this->Reaction->save($this->data)) {
				$this->Session->setFlash(__('The reaction has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The reaction could not be saved. Please, try again.', true));
			}
		}
	}

	function thermal-age.localhost/admin/istrator_edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid reaction', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Reaction->save($this->data)) {
				$this->Session->setFlash(__('The reaction has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The reaction could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Reaction->read(null, $id);
		}
	}

	function thermal-age.localhost/admin/istrator_delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for reaction', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Reaction->delete($id)) {
			$this->Session->setFlash(__('Reaction deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Reaction was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>