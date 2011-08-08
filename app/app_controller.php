<?php

App::import('Sanitize');

class AppController extends Controller {
    var $components = array('Minify.Minify', 'Session');
    var $helpers = array ('Html','Form','Javascript','Minify.Minify','Session');
    
    function __construct () {
        parent::__construct();
        
    }


    function beforeFilter () {
        parent::beforeFilter();

        $this->set('global_minified_javascript',$this->Minify->js(array(
              /*prod:'js/wizard_components.js',*/
            'js/jquery-1.5.1.min.js',
            'js/jquery.once.js',
            'js/jquery-ui-1.8.14.custom.min.js',
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
