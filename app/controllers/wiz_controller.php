<?php

class WizController extends AppController {
    var $helpers = array ('Html','Form','Javascript','Minify.Minify');
    var $components = array ('Wizard.Wizard');
    var $uses = array('Specimen', 'Reaction', 'Site', 'Temporothermal', 'Citation', 'Job', 'Spreadsheet');

    var $amWizard = ''; // contains the name of the current wizard
    var $wizardInfos = array (
        'steps' => array (
            'dna_survival_screening_tool' => array (
                'specimen' => array (),
                'reaction' => array (
                    'showfield' => 'Reaction.showname'
                ),
                'site' => array (),
                'burial' => array (),
                'storage' => array (),
                'review' => array (),
            ),
            'thermal_age_spreadsheet_tool' => array (
                'spreadsheet_setup' => array (
                    'showfield' => 'Spreadsheet.name'
                ),
                'reaction' => array (
                    'showfield' => 'Reaction.showname'
                ),
                'spreadsheet_download' => array (),
                'spreadsheet_upload' => array (),
                'review' => array (),
            ),
        ),
        'titles' => array (
            'dna_survival_screening_tool' => 'DNA Survival Tool',
            'thermal_age_spreadsheet_tool' => 'Thermal Age Spreadsheet Tool'
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
                $lastWas = '';
                foreach ($steps as $stepName => &$stepInfo) {
                    $num['steps']++;
                    $sd = $this->Wizard->read($stepName);
                    if (is_array($sd)) { // there are some data set in this step
                        $num['complete']++;
                        $stepInfo['class'] = "complete";
                        $stepInfo['sfval'] = print_r ($this->Wizard->read($stepName .".". $stepInfo['showfield']), true);
                        $lastWasComplete = true;
                        $lastWas = $stepName;
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
                $this->wizardInfos['prevstep'] = (strlen ($lastWas) > 0) ? $lastWas : false;
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
        $this->Session->setFlash ("Cleared Cache & Reset Wizard");
        $this->redirect (array ('controller' => 'wiz', 'action' => 'index'));
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
        $this->_keepalive();
    }

    /**
     *
     * @param string $place name to search for (/part of)
     * @return string JSON place records from geonames wikipedia search or anywhere else.
     */
    function place_search () {
        $place = (!empty ($this->params['form']['place'])) ? $this->params['form']['place'] : null;
        //$place = trim (Sanitize::paranoid($place, array (' ', ',', '-', '\'', '(', ')')));
        $places = (strlen ($place) > 0) ? $places = ClassRegistry::init('Geonames')->placeSearch($place) : array ();

        if (!empty ($places['geonames'])) {
            $this->loadModel('Upload');
            foreach ($places['geonames'] as &$cpl) {
                if (!empty ($cpl['thumbnailImg'])) {
                    // DEBUG!!!
                    //$cpl['thumbnailImg'] = "https://chesapeakejournal.files.wordpress.com/2012/02/muteswan-cygnusolor.jpg"; // <-- swan! \o/
                    $cpl['thumbnailImg'] = $this->Upload->passThrough ($cpl['thumbnailImg'], $cpl['title']);
                }
            }
        }

        $this->set ('places', (array) $places);

    }

    /**
     * Function to get the altitude used in the temperature models from which we draw our 0ka DP
     * Used to calculate the difference between DEM alt. and alt. of actual site for lapse rate correction.
     * @param string $source currently one of 'pmip2' or 'worldclim' (note the latter is much higher resolution)
     */
    function dem_lookup ($source = null) {
        
        // Load ttkpl
        App::import ('Vendor', 'ttkpl/lib/ttkpl');
        ob_clean(); // @TODO remove all echos from ttkpl!

        $source = trim (Sanitize::paranoid($source));
        if (empty ($source)) $source = null;
        
        $data = array ('error' => array(), 'data' => array());

        $lat = (!empty ($this->params['url']['lat'])) ? floatval ($this->params['url']['lat']) : null;
        $lon = (!empty ($this->params['url']['lon'])) ? floatval ($this->params['url']['lon']) : null;
        $loc = new \ttkpl\latLon(0,0);
        if ($lat === null || $lon === null || ($lat + 90) > 180 || ($lon + 180) > 360) {
            $data['error'][] = "Unparseable lat/lon.";
        }
        elseif ($loc->setLatLon($lat, $lon) == false) {
            $data['error'][] = "Unloadable lat/lon.";
        }
        else {
            $ok = false;
            switch ($source) {
                case null:
                case "pmip2":

                    $pmalt = new \ttkpl\pmip(\ttkpl\PMIP2::ALT_VAR, \ttkpl\PMIP2::T_PRE_INDUSTRIAL_0KA, \ttkpl\PMIP2::MODEL_HADCM3M2);
                    $elev = $pmalt->getElevationFromFacet ($loc);
                    //debug ($elev);
                    $data['data']['pmip2'] = round ($elev->getValue()->getValue()->getValue(), 4);
                    $data['error'] = array_merge ($data['error'], $pmalt->importer->error);
                    $ok = true; if ($source != null) break;
                case null:
                case "wordclim":

                    $wcalt = new \ttkpl\worldclim (\ttkpl\worldclim::ALT_VAR);
                    $elev = $wcalt->getElevationFromFacet ($loc);
                    $data['data']['worldclim'] = round ($elev->getValue()->getValue()->getValue(), 4);
                    //$data['data']['worldclim'] = round ($wcalt->getElevationFromFacet($loc), 4);
                    //debug ($wcalt->importer->_latToBILDPO($wcalt->importer->currentHeader['ULYMAP']));
                    //debug ($wcalt->importer->_latToBILDPO(-90));
                    $data['error'] = array_merge ($data['error'], $wcalt->importer->error);
                    $ok = true; if ($source != null) break;
                default:
                    if (!$ok) $data['error'][] = "Invalid source.";

            }
        }

        $this->set (compact ('data'));
        $this->layout = 'ajax';
        
    }
    
    function keepalive () {
        $this->_keepalive();
    }

    function _keepalive () {
        $lastTime = $this->Session->read ('keepalive.last');
        $firstTime = $this->Session->read ('keepalive.first');
        if (1) {
            $time = microtime(true);
            $this->Session->write ('keepalive.last', $time);
            if (!$lastTime) {
                $lastTime = $time;
            }
            if (!$firstTime) {
                $firstTime = $time;
                $this->Session->write ('keepalive.first', $firstTime);
            }
            $this->set('kept_alive_for', $this->_formatSecs ($time - $firstTime));
            //$this->set('kept_alive_for', );
        }
        
    }
    
    function _formatSecs ($s = 0.0) {

        $seconds = 0.0; $minutes = 0.0; $hours = 0.0;

        if ($s >= 60.0) {
            $seconds = $s % 60.0;
            $minutes = ($s - $seconds) / 60.0; $m = $minutes;
        }

        if ($minutes > 60.0) {
            $minutes = $minutes % 60.0;
            $hours = ($m - $minutes) / 60.0;
        }
        
        return sprintf ("%02d:%02d:%02d", $hours, $minutes, $seconds);
        
    }



    /**
     * Each of the wizards should check on pageload whether the client environment cookie is set:
     *  Cookie not set, is a browser        ->      redirect to index
     *  Cookie set, env. good               ->      load wizard
     *  Cookie set, env. bad or crawler     ->      show description of wizard & browser reqs.
     */
    function _checkEnvironment () {
        
        $environmentGood = true;

        if (!$this->Session->check ('wizenv')) {
            // the cookie testing flag hasn't been set yet, set it and redirect to see if its still there
            $environmentGood = false;
            $this->Session->write ('wizenv', 'CHECK');
            $this->redirect(array ('action' => 'env_check', $this->action));
        }

        return $environmentGood;
    }
    /**
     *
     * @param string $forwardAction the action to jump back to after checking browser etc. not shit.
     */
    function env_check ($forwardAction = null) {
        $this->set ('ie', preg_match ("/MSIE/", $_SERVER['HTTP_USER_AGENT']) ? true : false);
        $c = (!$this->Session->check ('wizenv')) ? false : true;
        $this->set ('cookie', $c);
        if ($c) $this->Session->write ('wizenv', 'CHECKED');

        // @todo SECURITY: This might potentially leak the names of private functions (but not allow them to be run)
        // this isn't really much of a security hole because the source is publicly available but just thought I'd mention it.
        $this->set ('redirectTo', (in_array ($forwardAction, get_class_methods ($this))) ? $forwardAction : '');

        
    }

    /**
     * Checks to see if the environment is a supported browser, if not returns false, if so returns
     * true if the environment is successfully initialised or false if there are errors.
     */
    function _initWizardEnvironment ($wizardAction = null) {

        // <hax /> this is here to force session to init before writing in initial env check
        $this->Session->write ('wizards.lasttime', time());
        
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

    function beforeRender () {
        parent::beforeRender ();
       //die (print_r ($this, true));

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
     * The thermal age spreadsheet tool calculates lots of thermal ages at once and populates a
     * spreadsheet for the user to download and further analyse.
     */
    function thermal_age_spreadsheet_tool ($step = null) {
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
        $this->loadModel('Temporothermal');
        $this->Temporothermal->set ($this->data);
        
        if ($this->Specimen->validates() == true && $this->Temporothermal->validates(array('fieldList' => array('stopdate_ybp'))) == true) {
            return true;
        }
        
        return false;
    }

    function _processSpreadsheetSetup () {
        $this->loadModel('Spreadsheet');
        $this->Spreadsheet->set ($this->data);
        
        if ($this->Spreadsheet->validates() == true) {
            return true;
        }

        return false;
    }
    /*
     * @TODO make this generic - add a param to accept the name of the wiz action to return to.
     */
    function tasti_skipupload ($retact = 'thermal_age_spreadsheet_tool') {
        $this->Wizard->reset ();
        
        $this->Wizard->save ('spreadsheet_setup', array ('Spreadsheet' => array ('name' => '(skipped)')));
        $this->Wizard->save ('reaction', array ('Reaction' => array ('showname' => '(skipped)')));
        $this->Wizard->save ('spreadsheet_download', array ('Spreadsheet' => array ('passed_download_step' => 1)));

        $this->redirect(array (
            'controller' => 'wiz',
            'action' => $retact,
            'spreadsheet_upload'
        ));
    }
    function _processSpreadsheetUpload () {
        if (empty ($this->data['Spreadsheet']['file'])) return false;
        
        //debug ($this->data); die();
        $file = $this->data['Spreadsheet']['file'];
        if (!$file['error'] == 0 || $file['size'] == 0) {
            $this->Session->setFlash ('Upload Failed: ERROR ' . $file['error']);
            return false;
        }
        else {

            // copy file to
            $fn = APP.WEBROOT_DIR.DS . 'spreadsheets/input-' . time() . '_' . preg_replace ("/_csv$/", ".csv", Inflector::slug ($file['name']));
            if (!copy($file['tmp_name'], $fn)) {
                $this->Session->setFlash ("Unable to move uploaded file. Check config.");
                return false;
            }
            else {
                $this->Wizard->save ('spreadsheet_csv', array ('Spreadsheet' => array ('filename' => $fn)));
                //debug ($this->Wizard->read ('spreadsheet_csv'));die();
                //debug (array ('spreadsheet_upload', array ('Spreadsheet' => array ('filename' => $fn)))); die();

                // success - setup job in data to override review actions
                $this->Wizard->save ('set_review', array ('Job' => array (
                    'parser_name' => 'thermal_age_csv',
                    'processor_name' => 'thermal_age_multi',
                    'reporter_name' => 'thermal_age_csv',
                )));

                return true;
            }

            //new \ttkpl\csvData(, $tr1)
            // NOT THE  BELOW ACTUALLY JUST GIVE JOB A FILENAME, IT IS A PARSER AFTER ALL
            // AND CODE IS MORE REUSABLE THIS NEW WAY.
            // parse the csv file into the data structure used by the one-shot dna screening wizard
            // this means more parser task code can be reused
            return false;
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


    function _prepareBurial () {
        $ssd = $this->Wizard->read('specimen.Temporothermal.stopdate_ybp');
        $this->set ('agebp', $ssd);

        $this->loadModel('Soil');
        $soils = array_merge (array ('0' => ' '), $this->Soil->find('list', array (
            'Soil.id',
            'Soil.name'
        )));
        
        $this->set(compact('soils'));
    }
    function _processBurial () {
        $this->loadModel('SoilTemporothermal');
        $this->Temporothermal->set ($this->data);
        $ok = TRUE;
        if (!$this->Temporothermal->validates()) {
            $ok = false;
        }
        $invalid = array ();
        $newST = array ();
        
        foreach ($this->data['SoilTemporothermal'] as $index => $soiltemporothermal) {
            if (isset ($soiltemporothermal['order']))
                $newST[$soiltemporothermal['order']] = $soiltemporothermal;
        }
        foreach ($this->data['SoilTemporothermal'] as $index => $soiltemporothermal) {
            if (!isset ($soiltemporothermal['order']))
                $newST[] = $soiltemporothermal;
        }
        $this->data['SoilTemporothermal'] = $newST;
        foreach ($this->data['SoilTemporothermal'] as $index => $soiltemporothermal) {
            $soiltemporothermal['order'] = 0;
            if (strlen (trim (str_replace ('0', '', implode ('', $soiltemporothermal)))) > 0 ) {
                $soiltemporothermal = array ('SoilTemporothermal' => $soiltemporothermal);
                $this->SoilTemporothermal->set ($soiltemporothermal);
                if (!$this->SoilTemporothermal->validates()) {
                    $invalid[$index] = $this->SoilTemporothermal->invalidFields ();
                }    
            }
        }
        
        if (!empty ($invalid)) {
            $ok = false;
            $this->SoilTemporothermal->validationErrors = $invalid;
            //$this->set ('invalidSoilTemporothermalFields', $invalid);
        }
        return $ok;
    }


    function _prepareSpreadsheetSetup () {
        $this->loadModel('Soil');
        $soils = array_merge (array ('0' => ' '), $this->Soil->find('list', array (
            'Soil.id',
            'Soil.name'
        )));
        $this->set(compact('soils'));
    }
    function get_blank_spreadsheet () {
        $this->autoRender = false;
        $ssOpts = $this->Wizard->read('spreadsheet_setup.Spreadsheet');
        $ssOpts['name'] = Inflector::slug ($ssOpts['name']);
        $ssOpts['Reaction'] = $this->Wizard->read('reaction.Reaction');

        //debug ($ssOpts); die();
        //APP.WEBROOT_DIR;
        $csv = $this->Spreadsheet->get_blank_spreadsheet ($ssOpts);
        $fn =  self::_commonFilenamePrefix() . ((empty ($ssOpts['name'])) ? 'unnamed-job' : $ssOpts['name']);
        
        header('Content-disposition: attachment; filename=' . $fn . '.csv');
        echo $csv;
    }
    function _prepareSpreadsheetDownload () {

        //echo debug ($this->Wizard->read ('')); die();

        $this->loadModel('Soil');
        $soils = array_merge (array ('0' => ' '), $this->Soil->find('list', array (
            'Soil.id',
            'Soil.name'
        )));
        $this->set(compact('soils'));
    }



    function _prepareStorage () {
        $ssd = $this->Wizard->read('burial.Temporothermal.startdate_ybp'); // bp to ad? why is there an ad in a bp field?! gah!
        $this->set ('excavatedbp', $ssd);
    }

    /**
     * [Wizard Completion Callback]
     */
	function _afterComplete() {
		
        $this->redirect(array('controller' => 'wiz', 'action' => 'create_job'));

	}

    function _prepareReview () {
        $this->set ('input', $this->Wizard->read());

        $sr = $this->Wizard->read('set_review');
        if ($sr) {
            $this->data['Job'] = $sr['Job'];
        }
    }
    


    function create_job () {
        $wizardData = $this->Wizard->read();
        $this->Job->create();

        $job = $wizardData['review'];
        $job['Job']['status'] = 0; // set pending
        $job['Job']['data'] = serialize ($wizardData);

        if ($this->Job->save ($job)) {
            // created job ok. reset the wizard and redirect to the job status page.
// DEBUG
            $this->Wizard->reset ();
            $this->Session->Setflash ("Do not close this window!", true);
            $this->redirect(array('controller'=>'jobs', 'action' => 'status', $this->Job->field('id')));
        }

        //$this->set ('wizdata', $wizardData);
    }

}