<?php
class UsersController extends AppController {

    var $name = 'Users';


    public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(array ('login','oauth'));
    }
        
    function login() {
        if ($this->Session->check('Auth.User')) {
            $this->redirect(array ('action' => 'profile'));
        }
        $of = $this->Session->read('oauth.fail');
        if (is_array($of)) {
            $this->set(compact('of'));
            $this->Session->delete('oauth.fail');
        }
        
        
        //Auth Magic
    }
    function logout() {
       $this->redirect($this->Auth->logout());
    }
    
    function profile () {
        $user = $this->Auth->user();
        if (!$user) {
            // not logged in??!
            $this->redirect($this->Auth->logout());
            return;
        }
        $this->loadModel('Job');
        $jobs = $this->Job->find('all',array (
            'conditions' => array (
                'Job.user_id' => $user['User']['id']
            ),
            'order' => array (
                'Job.id' => 'DESC'
            )
        ));
        $nJobs = array ();
        if (!!$jobs) {
            foreach ($jobs as $jin => $job) {
                if (!$this->Job->read(null, $job['Job']['id'])) continue;
                $nJobs[$jin] = $job;
                $fn = $this->Job->bgpGetJobFileName ('report');
                if (file_exists ($fn)) {

                    $results = file_get_contents ($fn);
                    $uns = @unserialize($results);
                    if ($uns !== false)
                        $nJobs[$jin]['Job']['results_file'] = $uns;
                    else
                        $nJobs[$jin]['Job']['results_file'] = array ('error', 'couldn\'t read results file');
                }
                else 
                    $nJobs[$jin]['Job']['results_file'] = array ('error' => 'couldn\'t find results file', 'file' => $fn);
            }
            $jobs = $nJobs;
        }
        
        $JSCs = $this->Job->statusCodes;
        $this->set(compact('user','jobs','JSCs'));
    }
    
    private function _loadOAuth () {
        
        App::import('Vendor', 'LHttp', array('file' => 'lem'.DS.'httpclient-2013-02-20'.DS.'http.php'));
        App::import('Vendor', 'LOAuth', array('file' => 'lem'.DS.'oauth-api-2013-02-20'.DS.'oauth_client.php'));

        $client = new oauth_client_class;
	$client->server = 'Google';
        //$client->debug = true;
        //$client->debug_http = true;
	$client->redirect_uri = Router::url(array(
            'controller' => 'users',
            'action' => 'oauth',
            'callback'
        ), true);
        
	$client->client_id = '642707952741.apps.googleusercontent.com';
	$client->client_secret = 'QCeN-NpJIOTqDy3dPVTANaDW';
        
        return $client;
    }
    
    // Redirect user to google oauth login
    function oauth ($cbflag=null) {
        if ($this->Session->check('Auth.User')) {
            $this->redirect(array ('action' => 'profile'));
        }
        
        $client = $this->_loadOAuth();
        
        // Prevents an possible infinite loop
        if ($cbflag == 'callback')
            $client->dontGoDialogAgainKthx = true;
        
                
        $client->scope = 'https://www.googleapis.com/auth/userinfo.email '.
		'https://www.googleapis.com/auth/userinfo.profile ' .
                'https://www.googleapis.com/auth/plus.login';
	if(($success = $client->Initialize()))
	{
		if(($success = $client->Process()))
		{
			if(strlen($client->authorization_error))
			{
				$client->error = $client->authorization_error;
				$success = false;
			}
			elseif(strlen($client->access_token))
			{
				$success = $client->CallAPI(
					'https://www.googleapis.com/oauth2/v1/userinfo',
					'GET', array(), array('FailOnAccessError'=>true), $userInfo);
				/*$success = $client->CallAPI(
					'https://www.googleapis.com/plus/v1/people/me',
					'GET', array(), array('FailOnAccessError'=>true), $userPlus);*/
			}
		}
		$success = $client->Finalize($success);
	}
	if($client->exit)
            exit;
        
        if (!$success) {
            $this->Session->setFlash(__('Login Error', true));
            $this->Session->write('oauth.fail',array("Error",$client->error,"DBG",$client->debug_output));
            $this->redirect(array('action' => 'login'));
        }
        else {
            // Does user exist?
            $this->User->recursive = 0;
            $egu = $this->User->find('first',array (
                'conditions' => array (
                    'User.oauth_linked' => $client->server . "#" . $userInfo->id
                )
            ));
            if (!$egu) {
                $this->loadModel('Upload');
                $this->User->create();
                $this->User->save (array (
                    'User' => array (
                        'name' => $userInfo->name,
                        'alias' => $userInfo->given_name,
                        'username' => $userInfo->id,
                        'password' => 'oauth-noauth',
                        'email_priv' => $userInfo->email,
                        'url' => $userInfo->link,
                        'photo' => $this->Upload->passThrough ($userInfo->picture, $userInfo->name),
                        'oauth_linked' => $client->server . "#" . $userInfo->id
                    )
                ));
                $this->User->recursive = 0;
                $egu = $this->User->read (null,$this->User->getLastInsertID());
            }
            
            $this->Auth->login($egu);
            //var_dump(array ($egu, $this->Auth->login($egu)));
            //exit;
            $this->redirect(array('action' => 'profile'));
        }
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