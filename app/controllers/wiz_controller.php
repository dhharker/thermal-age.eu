<?php

class WizController extends AppController {
    var $helpers = array ('Html','Form','Javascript','Minify.Minify');
    var $components = array ('Wizard.Wizard');
    var $layout = 'wizard';


    /**
     * The index action...
     *  Acts as the common point of entry to the different wizards
     *  Performs browser, js & cookie checks on client and sets "environment is good" cookie with results
     *  Displays a nice clear menu to navigate to one of the wizards
     *  Also show quick login box, "Register" link & 3 bullets (·free, ·storage of results, ·something else)
     *
     */
    function index () {

    }


    /**
     * Each of the wizards should check on pageload whether the client environment cookie is set:
     *  Cookie not set, is a browser        ->      redirect to index
     *  Cookie set, env. good               ->      load wizard
     *  Cookie set, env. bad or crawler     ->      show description of wizard & browser reqs.
     */
    function _checkEnvironment () {
        $environmentGood = true;
        return $environmentGood;
    }

    /**
     * Checks to see if the environment is a supported browser, if not returns false, if so returns
     * true if the environment is successfully initialised or false if there are errors.
     */
    function _initWizardEnvironment () {
        if ($this->_checkEnvironment () == true) {

            $this->layout = 'wizard';
            $this->set ('content_for_layout', 'I am a wizard!');
            
            $this->set('minified_javascript',$this->Minify->js(array(
              'js/wizard_components.js',
            )));
            
        }
    }

    /**
     * The curator wizard is for estimating k*t for a geolocated sample with a single burial context
     * and optional storage phase.
     */
    function dna_survival_screening_tool () {
        $this->set ('content_for_layout', 'I am not a wizard :-(');

        $this->_initWizardEnvironment();

        // wizardy stuff
        

    }
}