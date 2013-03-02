<?php
class UsersController extends AppController {

	var $name = 'Users';
        var $components = array ('Auth');

    function login() {
       //Auth Magic
    }
    function logout() {
       $this->redirect($this->Auth->logout());
    }
    
    private function _loadOpauth () {
        require_once (ROOT . DS . APP_DIR . DS . 'vendors' . DS . 'opauth'.DS.'lib'.DS.'Opauth'.DS.'opauth.conf.php');
        require_once (ROOT . DS . APP_DIR . DS . 'vendors' . DS . 'opauth'.DS.'lib'.DS.'Opauth'.DS.'Opauth.php');

        //define('CONF_FILE', dirname(__FILE__).'/'.'opauth.conf.php');
        //define('OPAUTH_LIB_DIR', dirname(dirname(__FILE__)).'/lib/Opauth/');

        $Opauth = new Opauth( $config );
    }
    
    // Redirect user to google oauth login
    function oauth () {
        $this->_loadOpauth();
    }
    
    // After user has been oauth'd by google, do stuff.
    function oacb ($strategy) {
        $this->_loadOpauth();
        
        $response = null;

        switch($Opauth->env['callback_transport']) {
                case 'session':
                        session_start();
                        $response = $_SESSION['opauth'];
                        unset($_SESSION['opauth']);
                        break;
                case 'post':
                        $response = unserialize(base64_decode( $_POST['opauth'] ));
                        break;
                case 'get':
                        $response = unserialize(base64_decode( $_GET['opauth'] ));
                        break;
                default:
                        echo '<strong style="color: red;">Error: </strong>Unsupported callback_transport.'."<br>\n";
                        break;
        }

        /**
         * Check if it's an error callback
         */
        if (array_key_exists('error', $response)) {
                echo '<strong style="color: red;">Authentication error: </strong> Opauth returns error auth response.'."<br>\n";
        }

        /**
         * Auth response validation
         * 
         * To validate that the auth response received is unaltered, especially auth response that 
         * is sent through GET or POST.
         */
        else{
                if (empty($response['auth']) || empty($response['timestamp']) || empty($response['signature']) || empty($response['auth']['provider']) || empty($response['auth']['uid'])) {
                        echo '<strong style="color: red;">Invalid auth response: </strong>Missing key auth response components.'."<br>\n";
                } elseif (!$Opauth->validate(sha1(print_r($response['auth'], true)), $response['timestamp'], $response['signature'], $reason)) {
                        echo '<strong style="color: red;">Invalid auth response: </strong>'.$reason.".<br>\n";
                } else {
                        echo '<strong style="color: green;">OK: </strong>Auth response is validated.'."<br>\n";

                        /**
                         * It's all good. Go ahead with your application-specific authentication logic
                         */
                }
        }


        /**
        * Auth response dump
        */
        $this->set(compact('response'));
        die();
        $this->redirect($this->Auth->logout());
        // See if local user account exists
        // Create it if not
        // Log the user in
    }
    

	function index() {
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->User->create();
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
	}

	function edit($id = null) {
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid user', true));
			$this->redirect(array('action' => 'index'));
		}
		if (!empty($this->data)) {
			if ($this->User->save($this->data)) {
				$this->Session->setFlash(__('The user has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}
	}

	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for user', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->User->delete($id)) {
			$this->Session->setFlash(__('User deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>