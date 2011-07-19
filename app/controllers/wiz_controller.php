<?php

class WizController extends AppController {
    var $helpers = array ('Html','Form','Javascript','Minify.Minify');
    var $components = array ('Wizard.Wizard');
    var $uses = array('specimen', 'reaction', 'site', 'temporothermal');


    

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
    function _initWizardEnvironment ($wizardAction = null) {
        if ($this->_checkEnvironment () == true) {
            $success = true;

            $this->layout = 'wizard';
            $this->set ('content_for_layout', 'I am a wizard!');

            if ($wizardAction !== null)
                $this->Wizard->initialize ($this, array (
                    'wizardAction' => $wizardAction
                ));

            /** @todo this must change with action once more than one wizard is >0% written */
            $this->Wizard->steps = array('specimen', 'reaction', 'site', 'temporothermal');

            // validates against models automatically if no cb
            $this->Wizard->autoValidate = true;

            return $success;
        }
        return false;
    }


    function beforeFilter() {

 
        parent::beforeFilter();
        
		
	}

    /**
     * The curator wizard is for estimating k*t for a geolocated sample with a single burial context
     * and optional storage phase.
     */
    function dna_survival_screening_tool ($step = null) {
        $environmentGood = $this->_initWizardEnvironment(__FUNCTION__);

        if ($environmentGood == true)
            $this->Wizard->process($step);

    }


    /**
     * Specimen input handler
     */
    function _XprocessSpecimen () {
        $this->specimen->set ($this->data);

        if ($this->specimen->validates()) {
            return true;
        }

        return false;
    }


    /**
     * Reaction input handler
     */
    function _XprocessReaction () {
        $this->reaction->set ($this->data);

        if ($this->reaction->validates()) {
            return true;
        }

        return false;
    }


    /**
     * Site input handler
     */
    function _XprocessSite () {
        $this->site->set ($this->data);
        return false;
        if ($this->site->validates()) {
            return true;
        }

        return false;
    }


    /**
     * [Wizard Completion Callback]
     */
	function _afterComplete() {
		
        $this->redirect(array('controller' => 'wiz', 'action' => 'disptest'));

	}


    function disptest () {
        $wizardData = $this->Wizard->read();
		extract($wizardData);

        $this->set ('wizdata', $wizardData);
    }

}