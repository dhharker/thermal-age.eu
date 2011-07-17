<?php
class UploadsController extends AppController {

	var $name = 'Uploads';

	function index() {
		$this->Upload->recursive = 0;
		$this->set('uploads', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid upload', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('upload', $this->Upload->read(null, $id));
	}

	function add() {

        if (!empty($this->data) &&
             is_uploaded_file($this->data['Upload']['file']['tmp_name'])) {
            $fileData = fread(fopen($this->data['Upload']['file']['tmp_name'], "r"),
                                     $this->data['Upload']['file']['size']);

            $this->data['Upload']['name'] = $this->data['Upload']['file']['name'];
            $this->data['Upload']['type'] = $this->data['Upload']['file']['type'];
            $this->data['Upload']['size'] = $this->data['Upload']['file']['size'];
            $this->data['Upload']['data'] = $fileData;

            if ($this->Upload->save($this->data)) {
				$this->Session->setFlash(__('The upload has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The upload could not be saved. Please, try again.', true));
			}
        }
        
		$citations = $this->Upload->Citation->find('list');
		$users = $this->Upload->User->find('list');
		$this->set(compact('citations', 'users'));
	}

    function download($id) {
        Configure::write('debug', 0);
        $file = $this->Upload->findById($id);

        header('Content-type: ' . $file['MyFile']['type']);
        header('Content-length: ' . $file['MyFile']['size']); // some people reported problems with this line (see the comments), commenting out this line helped in those cases
        header('Content-Disposition: attachment; filename="'.$file['MyFile']['name'].'"');
        echo $file['Upload']['data'];

        exit();
    }


	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid upload', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->Upload->save($this->data)) {
				$this->Session->setFlash(__('The upload has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The upload could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Upload->read(null, $id);
		}
		$citations = $this->Upload->Citation->find('list');
		$users = $this->Upload->User->find('list');
		$this->set(compact('citations', 'users'));
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for upload', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Upload->delete($id)) {
			$this->Session->setFlash(__('Upload deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Upload was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>