<?php

App::import('Sanitize');

class AppController extends Controller {
    var $components = array('Cookie','Minify.Minify', 'Session', 'RequestHandler', 'Auth');
    var $helpers = array (
        'Html',
        'Form',
        'Javascript',
        'Minify.Minify',
        'Session',
        'Icons',
        'Time'
    );
    
    function __construct () {
        parent::__construct();

    }

    static function _commonFilenamePrefix () {
        return "thermal-age.eu_" . date ('Y-m-d_H-i-s_');
    }

    function beforeFilter () {
        
        // Rename cookie
        $this->Cookie->name = 'taeu';
        
        // This while proper security comes slowly along
        if (in_array ($this->name, array ('Soils', 'Reactions', 'Sites', 'Users', 'Citations', 'Feedbacks', 'Jobs', 'Uploads', 'Groups', 'Pages', 'Specimens')) && in_array ($this->action, array ('edit', 'delete', 'index'))) {

            //$this->cakeError('error404');
        }
        App::import('Model', 'User');
        User::store($this->Auth->user());
        
        $this->set ('isMobile', $this->RequestHandler->isMobile());
        $this->set ('isAjax', $this->RequestHandler->isAjax());

        $this->set('logged_in_user', $this->Auth->user());
        
        $scripts = array(
              /*prod:'js/wizard_components.js',*/
            //'js/jquery-1.5.1.js',
            '/js/jquery-1.7.2.min.js',
            //'js/jquery-1.7.2.js',
            '/js/jquery.once.js',
            '/js/jquery-ui-1.8.14.custom.min.js',
            '/js/chosen.jquery.js',
            '/js/jquery.ba-resize.min.js',
            '/js/jquery.smooth-scroll.js',
            //'/js/jquery.form.js',
            '/js/config.js',
            //prod:'js/ui.js', // basic interaction stuffs
            '/js/jqf/jquery.form.js',
            '/js/jquery.titlecase2.js',
            //'js/pageslide/jquery.pageslide.min.js',
            //'js/adapt/adapt.js',
        );
        if (0)
            $this->set ('global_minified_javascript',$this->Minify->js($scripts));
        else
            $this->set ('global_javascript',$scripts);
            //$this->set ('scripts_for_layout', $scripts);
            //$this->helpers->javascript->link($scripts);
        
        parent::beforeFilter();
    }

    function redirect ($url, $status = null, $exit = true, $ignoreAjax = false) {
        if ($this->RequestHandler->isAjax () && !$ignoreAjax) {

            if ($url !== null) {
                $goto = Router::url($url, true);
                echo <<<OMG
                <script type="text/javascript">
                    window.location='$goto';
                </script>
OMG;
            }

            if ($exit) {
                $this->_stop();
            }
        }
        else
            return parent::redirect ($url, $status, $exit);
    }

    function  beforeRender() {
        parent::beforeRender();
        
    }
    
    function authoriseWrite ($modelName = null, $model_id = null, $uid_field = 'user_id') {
        if ($modelName === null)
            $modelName = $this->modelClass;
        if ($model_id === null && isset ($this->data) && is_array ($this->data) && isset ($this->data[$modelName]) && isset ($this->data[$modelName]['id']))
            $model_id = $this->data[$modelName]['id'];
        $model_id = $model_id + 0;
        if (!$this->loadModel($modelName)) return "Couldn't load model $modelName";
        $this->$modelName->id = $model_id;
        if (!$this->$modelName->hasField($uid_field)) return "Model doesn't have field $uid_field";
        if (!$this->$modelName->exists()) return "Row with id $model_id doesn't exist";
        $v = $this->$modelName->read($uid_field,$model_id);
        if (!$v || $v == '') return "$uid_field isn't set";
        if ($v[$modelName][$uid_field] != $this->Auth->user('id')) return "$modelName.$uid_field = {$v[$modelName][$uid_field]} != ".$this->Auth->user('id');
        return true;
    }

}
