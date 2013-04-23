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
        'jobdefaults' => array (
            'dna_survival_screening_tool' => array (
                'parser_name' => 'dna_screener',
                'processor_name' => 'thermal_age',
                'reporter_name' => 'dna_screener',
            ),
            'thermal_age_spreadsheet_tool' => array (
                'parser_name' => 'thermal_age_csv',
                'processor_name' => 'thermal_age_multi',
                'reporter_name' => 'thermal_age_csv',
            )
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
     * as the control bar (previous step, "load values from" etc.)
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
                
                $sn = array_keys ($steps);
                $sr = array_search($this->wizardInfos['step_requested'], $sn);
                if ($sr === false)
                    $this->wizardInfos['prevstep'] = (strlen ($lastWas) > 0) ? $lastWas : false;
                elseif (is_numeric ($sr) && $sr > 0)
                    $this->wizardInfos['prevstep'] = $sn[$sr - 1];
                else
                    $this->wizardInfos['prevstep'] = false;
            }
        }

        if ($num['steps'] > 0)
            $this->wizardInfos['progress'] = round (($num['complete'] / $num['steps']) * 100, 2);
        else
            $this->wizardInfos['progress'] = 3;
        
        
        if (1) {
            $loadValuesFrom = $this->_getLoadValuesFromOpts ();
            $this->set (compact ('loadValuesFrom'));
            $this->set ('loadValuesFromLast',$this->Session->read('wizards.lvf.'.$this->wizardInfos['wizardname'].'.last'));
            
            //print_r ($loadValuesFrom); die("asef");
        }
        return true;

    }
    
    function _getLoadValuesFromOpts ($addRecent = null, $wizName = null) {
        // find jobs where user has read access and p/p/r in Job match jobdefaults for this wizard
        // add "standard" values if applic
        // list order = "standard" job first then group jobs into Recently Used, My Jobs, Examples [public jobs; uid=0]
        
        if ($wizName === null) // This needed because wizard component reports amWizard as get_values_from_job_screen in context of re-generating call as called from that action/method
            $wizName = $this->amWizard;
        
        $cuid = $this->Auth->user('id');
        $sKey = 'wizards.lvf.'.$wizName;
        $session = $this->Session->read($sKey.'.cache');
        $recent = $this->Session->read($sKey.'.recent');
        if (empty ($recent)) $recent = array ();
        
        if ($addRecent !== null) {
            //$recent = array_filter($recent, function ($a) use ($addRecent) { return !!($a != $addRecent); });
            array_unique((array)array_unshift($recent, $addRecent));
            $this->Session->write($sKey.'.recent', $recent);
        }
        elseif (!!$session && 0) {
            return $session;
        }
        
        $cats = array (
            'Recently Used' => array (
                'Job.id' => $recent,
            ),
            'My Jobs' => array (
                'Job.user_id' => array ("$cuid", '0'),
                'NOT' => array ('Job.id' => $recent),
            ),
            'Published Jobs' => array (
                'Job.user_id NOT IN' => array ("$cuid",'0'),
                'Job.published' => '1',
                'DATE(Job.published_date) <=' => 'DATE(\''.date('Y-m-d').'\')',
            ),
            'Examples' => array (
                'Job.user_id' => '0',
            ),
        );
        
        $q = array (
            'conditions' => array (
                'Job.parser_name' => $this->wizardInfos['jobdefaults'][$wizName]['parser_name'],
                'Job.processor_name' => $this->wizardInfos['jobdefaults'][$wizName]['processor_name'],
                'Job.reporter_name' => $this->wizardInfos['jobdefaults'][$wizName]['reporter_name'],
            ),
            'order' => array (
                'Job.user_id',
                'Job.created DESC',
            ),
            'fields' => array (
                'Job.id',
                'Job.sel_title'
            ),
            'limit' => 10
        );
        
        $this->Job->recursive = -1;
        if (empty ($this->Job->virtualFields))
            $this->Job->virtualFields = array ();
        $this->Job->virtualFields['sel_title'] = 'CONCAT(id," (",LEFT(title,25),") ",DATE_FORMAT(created,\'%e%b%y\'))';
        $opts = array (
            array ('std' => "Standard Values")
        );
        foreach ($cats as $cat => $conds) {
            $opts[$cat] = $this->Job->find ('list', array_merge_recursive ($q, array ('conditions' => $conds)));
        }
        
        // Put recently used in recency order
        if (!empty ($opts['Recently Used'])) {
            $reordered = array ();
            foreach ($opts['Recently Used'] as $k => $v) {
                $ind = array_search($k, $recent);
                if ($ind !== false) {
                    $reordered["$k"] = $v;
                }
            }
            $opts['Recently Used'] = $reordered;
        }
        
        foreach ($opts as $l => $o) {
            if (empty ($o))
                unset ($opts[$l]);
        }
        
        $this->Session->write ($sKey.'.cache', $opts);
        
        return $opts;
        
    }
    
    function _getStandardValues ($wiz_name) {
        switch ($wiz_name) {
            case "dna_survival_screening_tool":
                // is job 667 in dev DB
                $s = <<<HAX
a:6:{s:8:"specimen";a:2:{s:8:"Specimen";a:3:{s:4:"name";s:14:""Standard" Run";s:4:"code";s:5:"STD-0";s:11:"description";s:122:"This run contains standard values for use as "sane defaults" when comparing specimens where some variables may be unknown.";}s:14:"Temporothermal";a:1:{s:12:"stopdate_ybp";s:4:"4000";}}s:8:"reaction";a:1:{s:8:"Reaction";a:9:{s:8:"showname";s:23:"DNA Depurination (Bone)";s:11:"reaction_id";s:1:"1";s:13:"molecule_name";s:0:"";s:13:"reaction_name";s:0:"";s:14:"substrate_name";s:0:"";s:4:"name";s:0:"";s:13:"ea_kj_per_mol";s:0:"";s:5:"f_sec";s:0:"";s:11:"citation_id";s:1:"3";}}s:4:"site";a:1:{s:4:"Site";a:7:{s:4:"name";s:27:"S-Block, University of York";s:7:"lat_dec";s:17:"53.94679973582498";s:7:"lon_dec";s:19:"-1.0580287460327327";s:9:"elevation";s:2:"14";s:16:"elevation_source";s:9:"Wikipedia";s:13:"lapse_correct";s:1:"1";s:11:"description";s:323:"The University of York (informally York University, or simply York, abbreviated as Ebor. for post-nominals), is an academic institution located in the city of York, England. Established in 1963, the campus university has expanded to more than thirty departments and centres, covering a wide range of (...)
(elevation: 14 m)";}}s:6:"burial";a:3:{s:14:"Temporothermal";a:5:{s:13:"startdate_yad";s:4:"2005";s:13:"startdate_ybp";s:3:"-55";s:11:"temp_mean_c";s:0:"";s:13:"temp_pp_amp_c";s:0:"";s:11:"description";s:0:"";}s:6:"Burial";a:1:{s:9:"numLayers";s:1:"1";}s:18:"SoilTemporothermal";a:1:{i:0;a:5:{s:7:"soil_id";s:1:"1";s:11:"thickness_m";s:3:"1.5";s:6:"sudden";s:1:"0";s:15:"direct_sunlight";s:1:"0";s:5:"order";s:1:"0";}}}s:7:"storage";a:1:{s:14:"Temporothermal";a:6:{s:13:"startdate_yad";s:4:"2013";s:13:"startdate_ybp";s:3:"-63";s:12:"stopdate_ybp";s:3:"-55";s:11:"temp_mean_c";s:2:"10";s:13:"temp_pp_amp_c";s:2:"10";s:11:"description";s:0:"";}}s:6:"review";a:1:{s:3:"Job";a:3:{s:14:"processor_name";s:11:"thermal_age";s:11:"parser_name";s:12:"dna_screener";s:13:"reporter_name";s:12:"dna_screener";}}}
HAX;
                break;
            case "thermal_age_spreadsheet_tool":
                $s = <<<HAX
a:3:{s:17:"spreadsheet_setup";a:1:{s:11:"Spreadsheet";a:6:{s:4:"name";s:10:""Standard"";s:15:"soil_cols_count";s:1:"2";s:13:"example_soils";a:2:{i:0;s:1:"1";i:1;s:1:"6";}s:9:"sine_cols";s:1:"0";s:16:"multi_tt_example";s:1:"0";s:20:"custom_kinetics_cols";s:1:"0";}}s:8:"reaction";a:1:{s:8:"Reaction";a:9:{s:8:"showname";s:23:"DNA Depurination (Bone)";s:11:"reaction_id";s:1:"1";s:13:"molecule_name";s:0:"";s:13:"reaction_name";s:0:"";s:14:"substrate_name";s:0:"";s:4:"name";s:0:"";s:13:"ea_kj_per_mol";s:0:"";s:5:"f_sec";s:0:"";s:11:"citation_id";s:1:"3";}}s:20:"spreadsheet_download";a:1:{s:11:"Spreadsheet";a:1:{s:20:"passed_download_step";s:1:"1";}}}
HAX;
                break;
            default:
                return array ();
        }
        
        return unserialize (trim ($s));
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
        $this->Session->delete ("wizards.currently");
        $this->Session->delete ("wizards.after_save");
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
     * Loads data from a previous job into a single screen of the wizard and reloads the form
     */
    function get_values_from_job_screen () {
        
        $job_id =       (!empty ($this->params['url']['job_id'])) ?     $this->params['url']['job_id'] : null;
        $wiz_name =     (!empty ($this->params['url']['wiz_name'])) ?   $this->params['url']['wiz_name'] : null;
        $wiz_screen =   (!empty ($this->params['url']['wiz_screen'])) ? $this->params['url']['wiz_screen'] : null;
        
        
        $this->autoLayout = false;
        $this->autoRender = false;
        $op = array (
            'vals' => array (),
            'error' => false
        );
        if ($job_id !== null && $wiz_screen !== null && ($this->Job->idExists ($job_id) || $job_id == 'std')) {
            if ($job_id != 'std' && $this->authoriseRead('Job', $job_id) !== true) {
                $op['error'] = "Not authorised.";
            }
            else {
                if ($job_id == 'std') {
                    $u = $this->_getStandardValues ($wiz_name);
                }
                else {
                    $d = $this->Job->read ('data', $job_id);
                    $u = unserialize ($d['Job']['data']);
                }
                if ($u === false) {
                    $op['error'] = "Couldn't decode job data.";
                }
                elseif (!isset ($u[$wiz_screen])) {
                    $op['error'] = "That job doesn't contain data for this wizard screen.";
                }
                else {
                    $op['vals'] = $u[$wiz_screen];
                }
            }
        }
        else {
            $op['error'] = "Ensure job_id, wiz_name and wiz_screen params are set.";
        }
        
        if ($op['error'] === false) {
            $this->Wizard->save ($wiz_screen, $op['vals']);
            $this->Session->setFlash ("Copied data from job $job_id");
            // Regenerate the "load from" list options, adding this job to recent items
            $this->_getLoadValuesFromOpts($job_id, $wiz_name);
            // Most recently used selected on load
            $lastKey = 'wizards.lvf.'.$wiz_name.'.last';
            $this->Session->write ($lastKey, $job_id);
            
            $this->redirect(array (
                'controller' => 'wiz',
                'action' => $wiz_name,
                $wiz_screen
            ));
        }
        else {
            $this->Session->setFlash ("Error: " . $op['error']);
            
            $this->redirect(array (
                'controller' => 'wiz',
                'action' => $wiz_name,
                $wiz_screen,
            ));
            
        }
        
    }
    
    /**
     * Function to get the altitude used in the temperature models from which we draw our 0ka DP
     * Used to calculate the difference between DEM alt. and alt. of actual site for lapse rate correction.
     * @param string $source currently one of 'pmip2' or 'worldclim' (note the latter is much higher resolution)
     */
    function dem_lookup ($source = null) {
        
        // Load ttkpl
        App::import ('Vendor', 'ttkpl/lib/ttkpl');
        @ob_clean(); // @TODO remove all echos from ttkpl!

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
            // @TODO refactor this block somewhere sensible and update the equivalent lookups
            // during job processing.
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
    function _initWizardEnvironment ($wizardAction = null, $step = null) {

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

            $this->wizardInfos['step_requested'] = preg_replace ('/[\W]/','',strtolower ($step));
            
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
        return $this->_run_wizard(__FUNCTION__, $step);
    }
    /**
     * The thermal age spreadsheet tool calculates lots of thermal ages at once and populates a
     * spreadsheet for the user to download and further analyse.
     */
    function thermal_age_spreadsheet_tool ($step = null) {
        return $this->_run_wizard(__FUNCTION__, $step);
    }
    
    
    function _run_wizard ($wizard, $step = null) {
        if (!method_exists($this, $wizard)) return false;
        // is the requested wizard the running wizard?
        $wcKey = 'wizards.currently';
        $crw = $this->Session->read ($wcKey);
        if (!!$crw && $crw != $wizard) {
            $this->redirect(array (
                'controller' => 'wiz',
                'action' => 'switch_to',
                $wizard
            ));
            return;
        }
        
        $environmentGood = $this->_initWizardEnvironment($wizard, $step);
        
        if ($environmentGood !== true) return false;
        
        $wizardData = $this->Wizard->read();
        if (!!$wizardData && !empty ($wizardData))
            $this->Session->write ($wcKey, $wizard);
        
        $this->Wizard->process($step);
        
    }
    
    function resume_draft ($job_id) {
        if (!($this->Job->idExists ($job_id) && $this->authoriseWrite('Job',$job_id))) {
            $this->Session->setFlash ("Error: Unknown Job or not authorised.");
            $this->redirect (array (
                'controller' => 'users',
                'action' => 'dashboard'
            ));
        }
        elseif ($this->Session->check('wizards.currently')) {
            $this->redirect (array (
                'controller' => 'wiz',
                'action' => 'switch_to',
                $job_id
            ));
        }
        else {
            $this->Wizard->reset();
            $j = $this->Job->read(null,$job_id);
            $this->Wizard->restore (unserialize ($j['Job']['data']));
            $this->Job->delete ($job_id);
            $this->Session->setFlash ("Loaded unfinished job back into wizard: ".@$j['Job']['title']);
            $this->Session->write('wizards.currently',$j['Job']['wizard_name']);
            $this->redirect (array (
                'controller' => 'wiz',
                'action' => $j['Job']['wizard_name'],
                false
            ));
        }
    }
    
    function switch_to ($new_wizard) {
        $action =       (!empty ($this->params['url']['action'])) ?     $this->params['url']['action'] : null;
        if (!in_array ($new_wizard, array_keys ($this->wizardInfos['titles'])) && !(is_numeric ($new_wizard) && $this->authoriseRead ('Job', $new_wizard))) {
            $action = null;
            $this->Session->setFlash ("Warning: Unknown wizard or not authorised.");
        }
        
        switch ($action) {
            case 'save':
                $url = array (
                    'controller' => 'wiz',
                    'action' => 'save_draft',
                    null
                );
                $thenDo = is_numeric ($new_wizard) ? array ('resume_draft',$new_wizard) : array ($new_wizard);
                $this->Session->write('wizards.after_save', $thenDo);
                break;
            case 'discard':
                $this->Wizard->reset();
                $this->Session->delete('wizards.currently');
                if (is_numeric ($new_wizard))
                    $url = array (
                        'controller' => 'wiz',
                        'action' => 'resume_draft',
                        $new_wizard
                    );
                else
                    $url = array (
                        'controller' => 'wiz',
                        'action' => $new_wizard,
                        null
                    );
                break;
            case 'cancel':
                $url = array (
                    'controller' => 'wiz',
                    'action' => $this->Session->read('wizards.currently'),
                    null
                );
                break;
        }
        if ($action !== null)
            $this->redirect ($url);
        $this->set ('newWizard', $new_wizard);
    }
    
    function save_draft () {
        $this->_wizard_data_to_job(true);
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
                $this->_setJobDefaults ();
                
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
    
    function _setJobDefaults () {
        if (isset ($this->wizardInfos['jobdefaults'][$this->amWizard])) {
            $this->Wizard->save ('set_review', array ('Job' => $this->wizardInfos['jobdefaults'][$this->amWizard]));
            return true;
        }
        else return false;
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

        if (count ($ssOpts['example_soils']) > 0) {
            $this->loadModel('Soil');
            $this->Soil->recursive = -1;
            $soils = $this->Soil->find('all', array (
                'fields' => array (
                    'Soil.id',
                    'Soil.name',
                    'Soil.thermal_diffusivity_m2_day'
                ),
                'conditions' => array (
                    'Soil.id' => $ssOpts['example_soils']
                )
            ));
            $ssOpts['example_soils'] = $soils;
        }
        if ($ssOpts['Reaction']['reaction_id'] > 0) {
            $this->loadModel ('Reaction');
            $this->Reaction->recursive = -1;
            $reaction = $this->Reaction->find ('first', array (
                'fields' => array (
                    'Reaction.id',
                    'Reaction.name',
                    'Reaction.ea_kj_per_mol',
                    'Reaction.f_sec',
                ),
                'conditions' => array (
                    'Reaction.id' => $ssOpts['Reaction']['reaction_id']
                )
            ));
            $ssOpts = array_merge($ssOpts, $reaction);
        }
        //debug ($ssOpts); die();
        //APP.WEBROOT_DIR;
        $csv = $this->Spreadsheet->get_blank_spreadsheet ($ssOpts);
        $fn =  self::_commonFilenamePrefix() . ((empty ($ssOpts['name'])) ? 'unnamed-job' : $ssOpts['name']);
        
        header('Content-disposition: attachment; filename=' . $fn . '.csv');
        echo $csv;
    }
    function _prepareSpreadsheetDownload () {

        //echo debug ($this->Wizard->read ('')); die();

        // @TODO is this redundant?!
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
    
    /**
     * Used to turn wizard data into a job when a wizard is successfully completed
     */
    function create_job () {
        $this->_wizard_data_to_job();
    }
    
    /**
     * Saves wizard data in a new job, resets wizard and redirects
     * @param boolean $draft whether job is ready to run now (false) or saved for later (true)
     */
    function _wizard_data_to_job ($draft = false) {
        $wizardData = $this->Wizard->read();
        $this->Job->create();

        if (!$draft) $job = $wizardData['review'];
        $job['Job']['status'] = (!!$draft) ? 4 : 0; // set draft else pending
        $job['Job']['data'] = serialize ($wizardData);
        $job['Job']['wizard_name'] = $this->Session->read('wizards.currently');
        
        if (!is_array ($this->wizardInfos['jobdefaults'][$job['Job']['wizard_name']])) {
            $this->Session->setFlash ('Error: Couldn\'t find specification for wizard.');
        }
        else {
            foreach ($this->wizardInfos['jobdefaults'][$job['Job']['wizard_name']] as $k => $v) {
                $job['Job'][$k] = $v;
            }
        }
        
        if (empty ($job['Job']['title'])) {
            switch ($job['Job']['wizard_name']) {
                case "dna_survival_screening_tool":
                    $job['Job']['title'] = '';
                    if (@!empty ($wizardData['specimen']['Specimen']['code']))
                        $job['Job']['title'] .= $wizardData['specimen']['Specimen']['code'] . "; ";
                    if (@!empty ($wizardData['specimen']['Temporothermal']['stopdate_ybp']))
                        $job['Job']['title'] .= $wizardData['specimen']['Temporothermal']['stopdate_ybp'] . "yrs bp";
                    if (@!empty ($wizardData['site']['Site']['name'])) {
                        $nn = trim(substr($wizardData['site']['Site']['name'], 0, 25));
                        if ($nn != $wizardData['site']['Site']['name']) $nn .= '...';
                        if (!empty ($nn))
                            $job['Job']['title'] .= ", " . $nn;
                    }
                    break;
                case "thermal_age_spreadsheet_tool":
                    if (isset ($wizardData['spreadsheet_upload']['Spreadsheet']['file']['name']))
                        $job['Job']['title'] = $wizardData['spreadsheet_upload']['Spreadsheet']['file']['name'];
                    break;
                default:
                    $job['Job']['title'] = 'Untitled Job';
                    
            }
            
        }
        
        if (!$this->Job->save ($job)) {
            $this->Session->setFlash ("Error: Unable to save job!");
            $this->Session->delete ("wizards.after_save");
            $this->redirect(array ('controller' => 'wiz', 'action' => 'index', false));
        }
        // created job ok. reset the wizard and redirect to the job status page.
        $this->Wizard->reset ();
        $this->Session->delete ('wizards.currently');
        
        if (!$draft) {
            // (re-)Kick off processing in case thread has died
            $this->Job->tryProcessNext();
            $this->redirect(array('controller'=>'jobs', 'action' => 'status', $this->Job->field('id')));
        }
        else {
            $this->Session->setFlash ("Previous incomplete run saved.");
            $r = $this->Session->read('wizards.after_save');
            if (!empty($r)) {
                $r = (array) $r;
                $this->redirect(array_merge (array (
                    'controller' => 'wiz',
                    'action' => array_shift($r)
                ), $r));
                $this->Session->delete ("wizards.after_save");
            }
            else {
                $this->redirect (array (
                    'controller' => 'users',
                    'action' => 'dashboard'
                ));
            }
        }
    }

}