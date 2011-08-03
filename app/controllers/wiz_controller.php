<?php

class WizController extends AppController {
    var $helpers = array ('Html','Form','Javascript','Minify.Minify');
    var $components = array ('Wizard.Wizard', 'RequestHandler');
    var $uses = array('Specimen', 'Reaction', 'Site', 'Temporothermal', 'Citation');

    var $amWizard = ''; // contains the name of the current wizard
    var $wizardInfos = array (
        'steps' => array (
            'dna_survival_screening_tool' => array (
                'specimen' => array (),
                'reaction' => array (
                    'showfield' => 'Reaction.showname'
                ),
                'site' => array (),
                'temporothermal' => array (
                    'title' => 'Environment',
                ),
            ),
        ),
        'titles' => array (
            'dna_survival_screening_tool' => 'DNA Survival Tool'
        ),
        "wizardname" => '',
        "wizardtitle" => '',
        "progress" => 0,
        "stepname" => '',
        "steptitle" => '',
    );
    



    /**
     * This is called towards the end of _initWizardEnvironment and pads out $this->wizardInfos with
     * all the stuff needed by the wizard progress column (showing previous and next steps), as well
     * as the control bar (previous step etc.)
     * @param string $wizardAction
     * @return bool success
     * @todo take into account validation success when assigning css classes
     */
    function _initWizardInfos ($wizardAction) {
        // is it a real wizard?
        $wizardAction = strtolower (trim($wizardAction));
        if (strlen ($wizardAction) == 0) return false;
        if (in_array ($wizardAction, array_keys ($this->wizardInfos['steps']))) {
            // yes
            $this->amWizard = $wizardAction;
            $this->wizardInfos['wizardname'] = $wizardAction;
            $this->wizardInfos['wizardtitle'] = 
                (isset ($this->wizardInfos['titles'][$wizardAction])) ? $this->wizardInfos['titles'][$wizardAction] : inflector::humanize ($wizardAction);
            
        }
        else return false;

        $num = array ('steps' => 0, 'complete' => 0);

        foreach ($this->wizardInfos['steps'] as $wizName => &$steps) {
            foreach ($steps as $stepName => &$stepInfo) {
                if (!is_array ($stepInfo)) $stepInfo = array ();
                if (!isset ($stepInfo['title']) || strlen ($stepInfo['title']) == 0) {
                    $stepInfo['title'] = inflector::humanize ($stepName);
                }
                if (!isset ($stepInfo['showfield']) || strlen ($stepInfo['showfield']) == 0) {
                    $stepInfo['showfield'] = inflector::camelize ($stepName) . '.name';
                    $stepInfo['sfval'] = false;
                }
            }
            if ($wizName == $this->amWizard) {
                $lastWasComplete = true;
                foreach ($steps as $stepName => &$stepInfo) {
                    $num['steps']++;
                    $sd = $this->Wizard->read($stepName);
                    if (is_array($sd)) { // there are some data set in this step
                        $num['complete']++;
                        $stepInfo['class'] = "complete";
                        $stepInfo['sfval'] = print_r ($this->Wizard->read($stepName .".". $stepInfo['showfield']), true);
                        $lastWasComplete = true;
                    }
                    elseif ($lastWasComplete == true) {
                        $stepInfo['class'] = "current";
                        $this->wizardInfos['stepname'] = $stepName;
                        $lastWasComplete = false;
                    }
                    else {
                        $stepInfo['class'] = "future";
                    }

                    //$stepInfo['class'];
                }
            }
        }

        if ($num['steps'] > 0)
            $this->wizardInfos['progress'] = round (($num['complete'] / $num['steps']) * 100, 2);
        else
            $this->wizardInfos['progress'] = 3;


        return true;

    }
    /**
     * To fill in some wizard-wide stuff for use by the various views/elements. Run once we're sure
     * everything is ok.
     */
    function _initWizardInfo () {
        
    }


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

    // temporary
    function clearcache () {
        $this->set ('clearedOk', (clearCache ()) ? TRUE : FALSE);
        $this->set ('clearedWizard', ($this->Wizard->reset()) ? TRUE : FALSE);
    }

    /**
     * Displays the progress through the current wizard. Using info from this controller and the
     * state of the Wizard component, populates a view for display down the rhs of the wizard in
     * progress or stand-alone as the wizard re-entry start page (possibly).
     */
    function progress ($wizName = null) {
        $wizName = Sanitize::paranoid ($wizName, array ('-', '_'));
        $environmentGood = $this->_initWizardEnvironment($wizName);
        if (!$environmentGood) die ("Env bad $wizName");
        $this->set ('wizardInfos',$this->wizardInfos);
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

            $this->set ('isWizard', true);
            $this->set ('content_for_layout', 'I am a wizard!');
            
            if ($wizardAction !== null)
                $this->Wizard->initialize ($this, array (
                    'wizardAction' => $wizardAction
                ));

            if (!$this->_initWizardInfos($wizardAction)) {
                return false;
            }
            
            $this->Wizard->steps = array_keys ($this->wizardInfos['steps'][$this->amWizard]);
            $this->set ('wizardInfos', $this->wizardInfos);
            
            
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

        //$this->Session->setFlash (print_r ($this->data, true));
    }


    /**
     * Specimen input handler
     */
    function _processSpecimen () {
        $this->Specimen->set ($this->data);
        
        if ($this->Specimen->validates() == true) {
            return true;
        }
        
        return false;
    }


    /**
     * Reaction input handler
     */
    function _processReaction () {
        
        if ($this->data['Reaction']['reaction_id'] == -1) {
            $this->Reaction->set ($this->data);
            return ($this->Reaction->validates() == true) ? TRUE : FALSE;
        }
        elseif (!$this->Reaction->idExists ($this->data['Reaction']['reaction_id'])) {
            //$this->Reaction->validationErrors['Reaction.reaction_id'] = __("Invalid reaction ID.");
            $this->Reaction->invalidate('Reaction', __("Invalid reaction ID."));
            $this->Session->setFlash(__('Invalid Reaction ID!', true));
            return false;
        }
        return true;
        
    }
    function _prepareReaction () {
        $citations = $this->Reaction->Citation->find('list');
        $citations[0] = " ";
        $reactions = $this->Reaction->find('list');
        $reactions[-1] = "Custom";
		$this->set(compact('reactions', 'citations'));
    }

    /**
     * Site input handler
     */
    function _processSite () {
        $this->Site->set ($this->data);
        
        if ($this->Site->validates()) {
            return true;
        }

        return false;
    }
    function _prepareSite () {
        $citations = $this->Site->Citation->find('list');
        $citations[0] = " ";
		$this->set(compact('citations'));
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