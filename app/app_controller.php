<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */



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
            'js/jquery-ui-1.8.14.custom.min.js',
            'js/config.js',
            //prod:'js/ui.js', // basic interaction stuffs
            'js/jqf/jquery.form.js',
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
