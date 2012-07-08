<?php
class UploadsController extends AppController {
    var $name = 'Uploads';

	function index() {
		$this->Upload->recursive = 0;
		$this->set('uploads', $this->paginate());
	}

	function view($id = null) {
        if ($id == 0) {
            $id = -1;
        }
		$this->download ($id, 0);
	}

	function add() {

        if (empty($this->data)) {
            $this->Session->setFlash(__('No data.', true));
        }
        elseif (!isset ($this->data['Upload']['file']['tmp_name']) ||
            !is_uploaded_file($this->data['Upload']['file']['tmp_name'])) {
            
            $this->Session->setFlash(__('No file uploaded... feeeed meeeee dataaa!.', true));
            die (print_r ($this->data, TRUE));
        }
        else {
            $fileData = fread(fopen($this->data['Upload']['file']['tmp_name'], "r"),
                                     $this->data['Upload']['file']['size']);

            $this->data['Upload']['name'] = $this->data['Upload']['file']['name'];


            $finfo = new finfo;
            $mime = $finfo->file($this->data['Upload']['file']['tmp_name'], FILEINFO_MIME);
            //$this->data['Upload']['type'] = $this->data['Upload']['file']['type'];
            $this->data['Upload']['mime_type'] = $mime;


            $this->data['Upload']['size'] = $this->data['Upload']['file']['size'];
            $this->data['Upload']['file_contents'] = $fileData;
            if ($this->Upload->save($this->data)) {
				$this->Session->setFlash(__('The upload has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The upload could not be saved. Please, try again.', true));
			}
        }
        
		$citations = $this->Upload->Citation->find('list');
		//$users = $this->Upload->User->find('list');
		$this->set(compact('citations', 'users'));
	}

    function download($id, $force = 1) {
        if (!$id) {
			echo ""; return;
        }
        elseif ($id == -1) {
            // display error image
            $errfile = APP.WEBROOT_DIR.DS.'img'.DS.'test-fail-icon.png';
            $file = array (
                'Upload' => array (
                    'file_contents' => file_get_contents ($errfile),
                    'name' => basename ($errfile),
                    'size' => filesize ($errfile),
                    'mime_type' => 'image/png'
                )
            );
        }
        else {
            $file = $this->Upload->findById($id);
        }
        Configure::write('debug', 0);
        
        if (!empty ($file['Upload']['mime_type'])) header('Content-type: ' . $file['Upload']['mime_type']);
        if (!empty ($file['Upload']['size'])) header('Content-length: ' . $file['Upload']['size']);
        if ($force) header ('Content-Disposition: attachment; filename="'.$file['Upload']['name'].'"');
        echo $file['Upload']['file_contents'];
        $this->autoLayout = false;
        $this->autoRender = false;
        return false;
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