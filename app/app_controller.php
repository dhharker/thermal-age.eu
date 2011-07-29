<?php

App::import('Sanitize');

class AppController extends Controller {
    var $components = array('Minify.Minify', 'Session');
    var $helpers = array ('Html','Form','Javascript','Minify.Minify','Session');
    
    function __construct () {

        parent::__construct();
        
    }

    /**
     * Global stuff goes here
     */
    function beforeFilter () {
        parent::beforeFilter();

        $this->set('global_minified_javascript',$this->Minify->js(array(
              /*prod:'js/wizard_components.js',*/
            'js/jquery-1.5.1.min.js',
            'js/jquery.once.js',
            'js/jquery-ui-1.8.14.custom.min.js',
            'js/jquery.ba-resize.min.js',
            'js/config.js',
            //prod:'js/ui.js', // basic interaction stuffs
            'js/jqf/jquery.form.js',
            'js/jquery.titlecase2.js',
            //'js/pageslide/jquery.pageslide.min.js',
            'js/adapt/adapt.min.js',
            
            
        )));

        // The minify controller needs a blank layout otherwise it'll inherit from $layout above

        
    }

    function  beforeRender() {
        parent::beforeRender();
        //die ("<pre>".print_r ($this, TRUE));

        if ($this->name) {
            //$this->layout = '960';
        }
    }

}
