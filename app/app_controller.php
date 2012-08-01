<?php

App::import('Sanitize');

class AppController extends Controller {
    var $components = array('Minify.Minify', 'Session', 'RequestHandler');
    var $helpers = array (
        'Html',
        'Form',
        'Javascript',
        'Minify.Minify',
        'Session'
    );
    
    function __construct () {
        parent::__construct();

    }

    static function _commonFilenamePrefix () {
        return "thermal-age.eu_" . date ('Y-m-d_H-i-s_');
    }

    function beforeFilter () {

        // This while proper security comes slowly along
        if (in_array ($this->name, array ('Soils', 'Reactions', 'Sites', 'Users', 'Citations', 'Feedbacks', 'Jobs', 'Uploads', 'Groups', 'Pages', 'Specimens')) && in_array ($this->action, array ('edit', 'delete', 'index'))) {

            $this->cakeError('error404');
        }

        parent::beforeFilter();
        $this->set ('isMobile', $this->RequestHandler->isMobile());

        $this->set ('global_minified_javascript',$this->Minify->js(array(
              /*prod:'js/wizard_components.js',*/
            //'js/jquery-1.5.1.js',
            'js/jquery-1.7.2.min.js',
            //'js/jquery-1.7.2.js',
            'js/jquery.once.js',
            'js/jquery-ui-1.8.14.custom.min.js',
            'js/chosen.jquery.js',
            'js/jquery.ba-resize.min.js',
            'js/jquery.smooth-scroll.js',
            'js/config.js',
            //prod:'js/ui.js', // basic interaction stuffs
            'js/jqf/jquery.form.js',
            'js/jquery.titlecase2.js',
            //'js/pageslide/jquery.pageslide.min.js',
            //'js/adapt/adapt.js',
            
            
        )));
    }

    function redirect ($url, $status = null, $exit = true) {
        if ($this->RequestHandler->isAjax ()) {

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

}
