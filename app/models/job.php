<?php
/**
 * The Job model currently includes all the code for process management etc.; this should be moved
 * out at some point.
 *
 * When reading this code, remember all the _task functions will be run in a background process and
 * not as part of any http transaction.
 */
class Job extends AppModel {
	var $name = 'Job';
	var $displayField = 'title';
    var $statusCodes = array ('pending', 'running', 'finished', 'error', 'incomplete');

    var $maxThreads = 2; // maximum number of concurrent bg processors at a time
    var $sleepyTime = 2; // number of seconds to wait before checking for new job and starting it
    // The number of row·samples before the spreadsheet processor is eating all the RAM
    var $criticalRowSampleThreshold = 15000; // 30 rows * 500 years sampled in each; // live
    //var $criticalRowSampleThreshold = 900; // 30 rows * 500 years sampled in each;
    
    private $jobDir = ''; // temporary folder for graph scratch, zipping etc.
  
    private $percentRatio = array (
        'parse'   => .05,
        'process' => .9,
        'report'  => .05
    );
    
    /**
     * Convenience functions for AJAX job list update, but generic in nature
     */
    function findJobsGetResultsFile($options = array ()) {
        $jobs = $this->find('all',$options);
        $nJobs = array ();
        if (!!$jobs) {
            foreach ($jobs as $jin => $job) {
                if (!$this->read(null, $job['Job']['id'])) continue;

                $nJobs[$jin] = $job;
                $fn = $this->bgpGetJobFileName ('report');
                if (file_exists ($fn)) {

                    $results = file_get_contents ($fn);
                    $uns = @unserialize($results);
                    if ($uns !== false)
                        $nJobs[$jin]['Job']['results_file'] = $uns;
                    else
                        $nJobs[$jin]['Job']['results_file'] = array ('error', 'couldn\'t read results file');
                }
                else 
                    $nJobs[$jin]['Job']['results_file'] = array ('error' => 'couldn\'t find results file', 'file' => $fn);
            }
            return $nJobs;
        }
        return false;
    }
    function getSectionsByUserId ($sections = null, $user_id = null, $since_epoch = 0) {
        if ($user_id === null) {
            $uid = User::get('id');
            if (!!$uid) $user_id = $uid;
        }
        $timeConstraint = "DATE(Job.updated) > DATE('".date('Y-m-d H:i:s',$since_epoch)."')";
        $jobSections = array (
            'recent' => $this->findJobsGetResultsFile(array (
                'conditions' => array (
                    'AND' => array (
                        'Job.user_id' => $user_id,
                        'Job.status !=' => '4',
                        //$timeConstraint // @TODO date constraint simply not working however i try it. ignoring for now.
                    )
                ),
                'order' => array (
                    'Job.updated' => 'DESC'
                ),
                'limit' => 4
            )),
            'incomplete' => $this->findJobsGetResultsFile(array (
                'conditions' => array (
                    'AND' => array (
                        'Job.user_id' => $user_id,
                        'Job.status =' => '4',
                        //$timeConstraint // (see above)
                        
                    )
                ),
                'order' => array (
                    'Job.updated' => 'DESC'
                ),
                'limit' => 4
            ))
        );
        if ($sections !== null) {
            $sections = (array) $sections;
            foreach ($jobSections as $ji => $js) {
                if (!in_array ($ji,$sections))
                    unset ($jobSections[$ji]);
            }
        }
        return $jobSections;
    }
    
    
    /**
     * To be run from CLI. Finds the next job in the queue and runs it.
     */
    function tryProcessNext () {

        // Global cleanup of crashed processes before we start
        $this->bgpGlobalCorpseCollection();

        //echo "Trying to process next job...\n";
        if (!$this->_goodToGo()) {
            if (PHP_SAPI == 'cli') echo $this->maxThreads . " process(es) already running?\n";
            return false;
        }
        if (PHP_SAPI !== 'cli') {
            // DEBUG
            //die("nofork.");
            return $this->_forkToBackground ();
        }
        
        $next = $this->_getNext ();
        
        if (!$next) {
            if (PHP_SAPI == 'cli') $this->_addToStatus ("No more jobs to process.\n");
        }
        else {
            
            $error = false;
            
            $this->read (null, $next['Job']['id']);

            $this->_startProcessing();
            
            $input = unserialize ($this->field('data'));
            
            // Start the next one (if it exists) now we've updated the status in the DB
            // This is here so that multiple samples can be run at once if maxThreads > 1
            //if (!(isset($input['resume']) && !!$input['resume'])) $this->_forkToBackground ();
            
            // the parser loads user input in whatever-ass format into the ttkpl object model
            $parsed = $this->_task ($this->field ('parser_name'), 'parser',     $input);
            
            // If the job will need to be resumed then save this info in db now and decrease priority for next run
            $resume = false;
            if (isset ($parsed['resume']) && is_array ($parsed['resume'])) {
                $resume = true;
                $input['resume'] = $parsed['resume'];
                $this->save(array (
                    'Job' => array (
                        'id' => $this->id,
                        'data' => serialize ($input),
                        'priority' => $this->field('priority')+1
                    )
                ),false);
                $this->read(null,$this->id);
            }
            //$this->_addToStatus("XXXXXX   PARSED RESUME:" . print_r ($parsed['resume'], true));
            
            // the processor takes this bunch of objects and makes them work
            $output = $this->_task ($this->field ('processor_name'), 'processor',  $parsed);
            // assuming nothing went wrong with the above steps, the reporter makes nice graphs and pdfs
            $report = $this->_task ($this->field ('reporter_name'), 'reporter',   $output);
            // Save number of rows reported by the reporter
            if (isset ($report['resume']) && is_array ($report['resume'])) {
                $resume = true;
                $data = unserialize ($this->data['Job']['data']);
                if (!!$data) $data = $input;
                $data['resume'] = $report['resume'];
                $j = array ('Job' => array (
                    'id' => $this->id,
                    'data' => serialize ($data),
                    'priority' => $this->field('priority')+1
                ));
                //$this->_addToStatus("XXXXXX   SAVE:" . print_r ($j, true));
                $this->save($j,false);
                $this->read(null,$this->id);
                
                // @todo make rowsParsed into rowsReported once it agrees to actually save this number fs
                if ($data['resume']['rowsParsed'] >= $data['resume']['nRows'] ||
                    $data['resume']['nRows'] <= $data['resume']['nPerBatch']) {
                    $j['Job']['status'] = 0;
                    $resume = false;
                }
                
                
                $newData = (!empty ($this->data['data'])) ? unserialize($this->data['data']) : array ();
                //$this->_addToStatus("XXXXXX   LOAD:" . print_r ($newData['resume'], true));
            }
            //$this->_addToStatus("XXXXXX   REPORT RESUME:" . print_r ($report['resume'], true));
            $this->_stopProcessing($error, $resume);
        }

        // After finishing a job, wait for the dust to settle and then see if there's another
        // job to process. The process spawned here will just die if there is no more work to do.
        //$this->_addToStatus("Resting for {$this->sleepyTime} seconds.");
        //sleep ($this->sleepyTime);
        
        $next = $this->_getNext ();
        if (!!$next) {
            $this->_addToStatus("There appears to be more to do.");
            $this->_forkToBackground();
        }
        else
            $this->_addToStatus("There doesn't appear to be anything more to do.");
        // Quit, either way. No longer stays alive and waiting for new hits as relying on web app to trigger this method
        exit (0);
        

    }
    function _getNext () {
        return $this->find('first', array (
            'order' => array (
                'Job.priority',
                'Job.id ASC',
            ),
            'conditions' => array (
                'Job.status' => 0
            )
        ));
    }
    function _getReaction ($id) {
        return $this->Reaction->find('first', array (
            'conditions' => array (
                'Reaction.id' => $id
            )
        ));
    }
    function _getSoil ($id) {
        return $this->Soil->find('first', array (
            'conditions' => array (
                'Soil.id' => $id
            )
        ));
    }

    /**
     * Checks to see if Job::_task_$name_$type exists and calls it with $args & returns retval
     * @param string $name of task (e.g. thermal_age)
     * @param string $type of task (i.e. parser, processor or reporter)
     */
    function _task ($name, $type, $args = array ()) {
        // processors have default parsers and reporters so if these aren't specified then use defaults
        if ($type != 'processor' && $name == '') {
            $name = $this->_task ($this->field('processor_name'), $type, array ("get_" . $type));
        }
        $meth = "_task_{$name}_{$type}";
        if (method_exists($this, $meth)) {
            try {
                $res = $this->$meth ($args);
            }
            catch (\Exception $e) {
                $this->_addToStatus (sprintf ("!! TTKPL Fatal Error (%s):\n%s\nLine: %d %s", $e->getCode(), $e->getMessage(), $e->getLine(), basename($e->getFile())));
                return false;
            }
            return $res;
        }
        else {
            $this->_addToStatus("Error: Unknown task.");
            return false;
        }
    }

    function _task_thermal_age_processor ($args) {
        $args = (array) $args;
        if (isset ($args[0]) && $args[0] == 'get_parser') return "dna_screener"; elseif (isset ($args[0]) && $args[0] == 'get_reporter') return "dna_screener"; // <-- default parser/reporter
        $this->_addToStatus ("Processor: Thermal Age");
        
        $ta = new \ttkpl\thermalAge();
        $ta->setKinetics($args['kinetics']);
        foreach ($args['Temporothermals'] as $tt) {
            if ($tt->rangeYrs > 0) {
                $ta->addTemporothermal ($tt);
            }
            else {
                $this->_addToStatus("Skipping temporothermal with zero timerange.");
            }
        }

        //print_r ($this->cleanse ($args));

        $this->_addToStatus("Calculating thermal age. This can take a long time, please be patient...");

        $taYrs = $ta->getThermalAge();

        $this->_addToStatus("Thermal age: " . $taYrs->getValue());

        //$this->cleanse ($ta);

        return array ('thermalAge' => $ta, 'thermalYears' => $taYrs, 'objects' => array ($ta));
    }
    function _task_dna_screener_parser ($args) {
        $abort = false;
        static $temps = null;
        $this->_addToStatus ("Parser: DNA Screener");
        $parsed = array ();
        $parsed['Temporothermals'] = array (); // pretty much everything ends up in here

        // reaction
        $r = false;
        if (isset ($args['reaction']) && isset ($args['reaction']['Reaction']) && isset ($args['reaction']['Reaction']['reaction_id']))
            $r = $this->_getReaction($args['reaction']['Reaction']['reaction_id']);
        if ($r !== false) {
            $kinetics = new \ttkpl\kinetics(
                $r['Reaction']['ea_kj_per_mol'],
                $r['Reaction']['f_sec'],
                $r['Reaction']['name'] . " (Source: {$r['Citation']['name']} [{$r['Citation']['id']}])"
            );
        }
        elseif (isset ($args['reaction'])) {
            $kinetics = new \ttkpl\kinetics(
                $args['reaction']['Reaction']['ea_kj_per_mol'],
                $args['reaction']['Reaction']['f_sec'],
                $args['reaction']['Reaction']['name']
            );
        }
        else {
            $this->_addToStatus("No valid kinetics, can't process this one");
            return false;
        }
        $parsed['kinetics'] = $kinetics;

        $this->_addToStatus("Kinetics: Done");

        // soils
        $bur = new \ttkpl\burial();
        $addbur = false;
        if ($args['burial']['Burial']['numLayers'] > 0) {
            $this->_addToStatus ("There are {$args['burial']['Burial']['numLayers']} burial layers in this TT. Encoding...\n");
            foreach ($args['burial']['SoilTemporothermal'] as $layerIndex => $layer) {
                
                
                $layerSoil = (!empty ($args['burial']['Soil'][$layerIndex])) ? $args['burial']['Soil'][$layerIndex] : false;
                // Assume Dh will be set explicitly
                $this->_addToStatus(print_r (compact ('layerSoil', 'layer'), 1));
                
                // Get soil just from DB?
                if (($layerSoil == false || empty ($layerSoil['thermal_diffusivity_m2_day'])) && isset ($layer['soil_id'])) {
                    $s = $this->_getSoil($layer['soil_id']);
                    if ($s !== false && $layer['thickness_m'] > 0) {
                        $std = \ttkpl\scalarFactory::makeThermalDiffusivity ($s['Soil']['thermal_diffusivity_m2_day']);
                        $z = \ttkpl\scalarFactory::makeMetres ($layer['thickness_m']);
                        $slayer = new \ttkpl\thermalLayer($z, $std, '');
                        $bur->addThermalLayer($slayer);
                        $addbur = true;
                    }
                    else {
                        if (!isset ($layer['order'])) $layer['order'] = 0;
                        $this->_addToStatus("Ignoring invalid soil layer " . $layer['order']);
                    }
                }
                elseif (!empty ($layerSoil['thermal_diffusivity_m2_day'])) {
                    // If the row is custom then the name will come from the soil name field, otherwise the name of the soil by id specified
                    if (!!$layer['custom']) {
                        $sName = $layerSoil['name'];
                    }
                    elseif (!empty ($layer['soil_id']) && $this->Soil->idExists($layer['soil_id'])) {
                        $snr = $this->Soil->read('name',$layer['soil_id']);
                        $sName = $snr['Soil']['name'];
                    }
                    // values have been specified
                    $std = \ttkpl\scalarFactory::makeThermalDiffusivity ($layerSoil['thermal_diffusivity_m2_day']);
                    $z = \ttkpl\scalarFactory::makeMetres ($layer['thickness_m']);
                    $slayer = new \ttkpl\thermalLayer($z, $std, $sName);
                    $bur->addThermalLayer($slayer);
                    $addbur = true;
                    $this->_addToStatus("Added custom soil layer: {$layer['thickness_m']}m of $sName (Dh = {$layerSoil['thermal_diffusivity_m2_day']})");
                }
                
                
            }
            //print_r ($bur);
            $this->_addToStatus("Encode {$args['burial']['Burial']['numLayers']} burial layers: Done");
        }
        else $this->_addToStatus("No burial layers specified");

    // storage temporothermal
        // @todo needs to support getting temperature data from uploaded CSV file stored in db
        $this->_addToStatus("Creating new temporothermal:");
        $tt = new \ttkpl\temporothermal();
        $this->_addToStatus("Setting time range:");
        $tt->setTimeRange(
            new \ttkpl\palaeoTime($args['storage']['Temporothermal']['startdate_ybp']),
            new \ttkpl\palaeoTime($args['storage']['Temporothermal']['stopdate_ybp'])
        );

        if ($tt->rangeYrs > 0) {
            $this->_addToStatus(sprintf ("Adding %0.1f years of storage", $tt->rangeYrs));
            $storageSine = new \ttkpl\sine ();
            $storageSine->setGenericSine (
                \ttkpl\scalarFactory::makeCentigradeAbs ($args['storage']['Temporothermal']['temp_mean_c']),
                \ttkpl\scalarFactory::makeKelvinAnomaly ($args['storage']['Temporothermal']['temp_pp_amp_c']),
                \ttkpl\scalarFactory::makeDays (0));
            $storageSine->desc = (empty ($args['storage']['Temporothermal']['description'])) ? 'Storage Sine' : $args['storage']['Temporothermal']['description'];
            $tt->setConstantClimate ($storageSine);
            $this->_addToStatus("Storage temperatures: Done");
        }
        $parsed['Temporothermals'][] = $tt;
        

    // burial temporothermal (inc. site, soils)
        $this->_addToStatus("Burial temporothermal:");
        $tt = new \ttkpl\temporothermal();
        $this->_addToStatus("Create temperatures:");
        if ($temps === null) $temps = new \ttkpl\temperatures(); // temperature database (it is literally this easy lol)

        $this->_addToStatus("Attach temperatures:");
        $tt->setTempSource($temps);
        $this->_addToStatus("Set timerange:");
        $tt->setTimeRange(
            new \ttkpl\palaeoTime($args['burial']['Temporothermal']['startdate_ybp']),
            new \ttkpl\palaeoTime($args['specimen']['Temporothermal']['stopdate_ybp'])
        );
        $this->_addToStatus(sprintf ("Range of %d years from %d to %d yrs bp. Chunk size: %d yrs, # yrs sampled: %d", $tt->rangeYrs, $tt->startDate->getYearsBp(), $tt->stopDate->getYearsBp(), $tt->chunkSize, floor ($tt->rangeYrs / $tt->chunkSize) ));
        $this->_addToStatus(sprintf ("Adding %0.1f years deposition pre-excavation", $tt->rangeYrs));
        $location = new \ttkpl\latLon (
            $args['site']['Site']['lat_dec'],
            $args['site']['Site']['lon_dec']
        );

        $location->desc = (empty ($args['site']['Site']['description'])) ? 'Location' : $args['site']['Site']['description'];
        $this->_addToStatus("Get localising corrections:");
        $localisingCorrections = $temps->getPalaeoTemperatureCorrections ($location);
        $tt->setLocalisingCorrections ($localisingCorrections);
        $tt->setLocation ($location); // This is just used for reporting at present
        if ($addbur == true) {
            if ($tt->setBurial ($bur))
                $this->_addToStatus("Attached burial conditions to burial temporothermal");
            else
                $this->_addToStatus("Couldn't attach burial conditions - possible error :-(");
        }
        else $this->_addToStatus("!! No burial during deposition?");

        // elevation correction
        $bestSiteElev = null;
        $bseSource = "";
        if (isset ($args['site']['Site']['lapse_correct']) && $args['site']['Site']['lapse_correct'] == 1) {
            // the lapse correct box applies to the known elev field, if its unchecked or value is
            // garbage then we lookup from worldclim anyway
            if (isset ($args['site']['Site']['elevation']) && is_numeric ($args['site']['Site']['elevation'])) {
                // we have a known elevation, w00t!
                $bestSiteElev = $args['site']['Site']['elevation'];
                $bseSource = $args['site']['Site']['elevation_source'];
                if (strlen($bseSource) == 0)
                    $bseSource = "(user supplied)";
            }
        }
        if ($bestSiteElev === null) {
            // not managed to get an elevation - get it from worldclim
            $wcalt = new \ttkpl\worldclim (\ttkpl\worldclim::ALT_VAR);
            $elev = $wcalt->getElevationFromFacet ($location);
            $bestSiteElev = round ($elev->getValue()->getValue()->getValue(), 4);
            $bseSource = "Worldclim";
        }
        // Get coarse elevation from pmip2 data
        $pmalt = new \ttkpl\pmip(\ttkpl\PMIP2::ALT_VAR, \ttkpl\PMIP2::T_PRE_INDUSTRIAL_0KA, \ttkpl\PMIP2::MODEL_HADCM3M2);
        $elev = $pmalt->getElevationFromFacet ($location);
        $coarseSiteElev = round ($elev->getValue()->getValue()->getValue(), 4);

        if ($coarseSiteElev != $bestSiteElev) {
            $tt->setAltitudeLapse($coarseSiteElev, $bestSiteElev);
            $tt->elevCorrection->desc .= " Site elevation source: $bseSource";
            $this->_addToStatus ("Applying elevation correction: " . $tt->elevCorrection->desc);
        }
        else {
            $this->_addToStatus("Unable to find any better site altitudes - not performing correction.");
        }

        $parsed['Temporothermals'][] = $tt;
        
        if ($abort) return false;
        
        //file_put_contents (APP . DS . "dsdbg.txt", print_r ($this->cleanse ($parsed), true));
        //print_r ($parsed);
        return $parsed;
    }

    function _task_thermal_age_multi_processor ($args) {
        //print_r ($this->cleanse ($args)); die();
        $this->_addToStatus("Processor: Thermal Age Multi-Specimen");
        // This should just run the screener a whole load of times and dump the output
        $results = array ();
        $unParsed = array ();
        $parsed = array ();
        
        if (!empty ($args['parsed'])) {
            
            foreach ($args['parsed'] as $running => $runIt) {
                if ($runIt == false) {
                    $this->_addToStatus ("Couldn't process {$args['unParsed'][$running]['specimen']['code']} as it failed to parse.");
                    
                }
                else {
                    //print_r ($this->cleanse($runIt));
                }
                
                if (!!$runIt) {

                    $this->_addToStatus("Deferring to Thermal Age processor for {$args['unParsed'][$running]['specimen']['code']}");
                    //print_r ($this->cleanse ($runIt)); die();
                    //$unParsed[] = $args['unParsed'][$running];
                    //$parsed[] = $runIt;
                    $stime = microtime(1);
                    //print_r ($args['unParsed'][$running]);

                    $res = $this->_task_thermal_age_processor($runIt);

                    $this->_addToStatus (sprintf ("Took %04.2fs", microtime(1) - $stime));
                    $ta = $res['thermalAge'];
                    $results[$running] = array (
                        'λ' => $ta->getLambda(),
                        '(1/λ)+1' => 1 + (1 / $ta->getLambda()),
                        'k (yr)' => $ta->getKYear (),
                        'k (sec)' => $ta->getKSec (),
                        'Teff' => \ttkpl\scalarFactory::makeCentigradeAbs ($ta->getTeff ())->getValue(),
                        '10C Thermal age' => $ta->getThermalAge()->getValue(),
                    );
                    /*$results[] = array (
                        '10c_thermal_age' => round ($res['thermalAge']->getThermalAge()->getValue(), 0),
                        'effective_temperature' => round ($res['thermalAge']->getTEff()->getValue() + ttkpl\scalarFactory::kelvinOffset, 2),

                    );*/
                    unset ($res);
                }
                $this->increaseJobPercentComplete('process');
            }
        }
        else {
            $this->_addToStatus("Nothing to do!");
        }


        $args['output_spreadsheet_filename'] = preg_replace ('/\/input(\W)/', '/output$1', $args['spreadsheet_csv']['Spreadsheet']['filename']);
        $this->_addToStatus(sprintf ("Output spreadsheet filename will be: %s", $args['output_spreadsheet_filename']));
        return array (
            //'unParsed' => $unParsed,
            //'parsed' => $parsed,
            'resume' => (isset ($args['resume'])) ? $args['resume'] : false,
            'xref' => $args['xref'],
            'results' => $results,
            'output_csv_url' => DS.'spreadsheets'.DS.basename ($args['output_spreadsheet_filename']),
            'output_csv_name' => basename ($args['output_spreadsheet_filename']),
            'output_csv_filename' => $args['output_spreadsheet_filename'],
            'spreadsheet_csv' => $args['spreadsheet_csv']
        );
    }
    // @TODO: (Braindump:) storage temporothermal isn't being added for some reason (fix this somewhere completely different)
    // @TODO: Might be fixed now...? Check!
    function _task_thermal_age_csv_reporter ($args) {
        mb_internal_encoding("UTF-8");
        $fn = @isset ($args['spreadsheet_csv']['Spreadsheet']['filename']) ? $args['spreadsheet_csv']['Spreadsheet']['filename'] : false;
        
        if (isset ($args['resume']) && is_array ($args['resume']) && isset ($args['resume']['rowsReported'])) {
            if (file_exists ($args['output_csv_filename'])) {
                $this->_addToStatus("Output file exists for resumable job; appending results to {$args['output_csv_filename']}");
                $fn = $args['output_csv_filename'];
            }
        }
        
        if (file_exists($fn)) {
            $this->_addToStatus(basename($fn) . " exists. Trying to open it...");
            $cp = new \ttkpl\csvData($fn, TRUE);
            //$cp->addColumn("10C Thermal Age");
            //$cp->addColumn("Effective Temperature");
            $this->_addToStatus("Headers found: " . implode ("|", $cp->titles));

            // slug  (and then detect headers (not all are required))
            $s2e = array ();
            foreach ($cp->titles as $title)
                $s2e[strtolower (Inflector::slug($title))] = $title;
            
            // Invert the xref index for quick searching
            //print_r (array_keys ($args)); die();
            $xref = array();
            foreach ($args['xref'] as $fpt => $dups) {
                foreach ($dups as $did => $dup) {
                    if ($did == 0) continue;
                    $xref[$dup] = $dups[0];
                }
            }
            //print_r ($xref);die(__LINE__);
            if (isset ($args['resume']) && is_array ($args['resume']) && isset ($args['resume']['rowsReported'])) {
                $this->_addToStatus("Resumable job. Fastforwarding output sheet by ".$args['resume']['rowsReported']." rows.");
                for ($i = 0; $i < $args['resume']['rowsReported']; $i++) {
                    $cp->next();
                }
            }
            else {
                $this->_addToStatus('Not a resumable job.');
            }
            
            $stop = false;
            do {
                $do = true;
                $this->_addToStatus("Result indices: " . count ($args['results']));
                if (isset ($args['results'][$cp->key()])) {
                    // Is not a dupe/is first of dupes
                    $cResult = $args['results'][$cp->key()];
                    $this->_addToStatus('Adding row');
                }
                elseif (isset ($xref[$cp->key()]) && isset ($args['results'][$xref[$cp->key()]])) {
                    $cResult = $args['results'][$xref[$cp->key()]];
                    $this->_addToStatus("Adding duplicate of row " . ($xref[$cp->key()] + 1) . " to row " . ($cp->key() + 1));
                }
                else {
                    $do = false;
                    $this->_addToStatus("No valid results found for row " . ($cp->key() + 1));
                }
                
                if (!!$do) {
                    foreach ($cResult as $col => $val)
                        $cp->setColumn($col, $val);
                }
                if (isset ($args['resume']) && is_array ($args['resume']) && isset ($args['resume']['rowsReported'])) {
                    //die ($this->_addToStatus(print_r ($args['resume'], true)));
                    $args['resume']['rowsReported']++;
                    $stop = $args['resume']['rowsReported'] >= $args['resume']['rowsParsed'] ? true : false;
                }
                $this->increaseJobPercentComplete('report');
            } while ($cp->next() && !$stop);
            
            $opfn = $args['output_csv_filename'];
            try {
                $ex = $cp->export($opfn);
                //@TODO: fix return value of export so it works
                //if ($ex) {
                $this->_addToStatus(sprintf ("Wrote output to %s", basename ($opfn)));
                /*}
                else {
                    $this->_addToStatus(sprintf ("Unknown error writing to %s (%s)", $opfn, $ex));
                }*/
            }
            catch (Exception $e) {
                $this->_addToStatus(sprintf ("Couldn't write output to %s: %s", $opfn, $e->getMessage()));
            }


        }
        
        file_put_contents ($this->bgpGetJobFileName ('report'), serialize ($args));
        // !!==borken
        //return true;
        if (isset ($args['resume']) && is_array ($args['resume'])) {
            
            /*$percent = ($args['resume']['rowsReported'] / $args['resume']['nRows']) * 100;
            $this->_addToStatus("($percent% complete)");
            $this->_updateJobPercentComplete($percent);
             * 
             */
            
            
            return array ('resume' => $args['resume']);
            
        }
        return false;
        
    }
    function _hrToOneDate  ($strHrDate, $strBestSuffix, $converter = null) {
        
        $nm = preg_match_all ('/(?:\s|^|:|\()([0-9]+)\s*(?:((to|-|─)|(±|\+\/(?:─|-)))\s*([0-9]+))?\s*('.$strBestSuffix.')?/i',$strHrDate, $m, PREG_SET_ORDER);
        if (count ($m) > 1) {
            $max = 0;
            $maxI = -1;
            foreach ($m as $mi => $mc) {
                $l = (!empty ($mc[6])) ? strlen (trim ($mc[6])) : 0;
                if ($l > $max) {
                    $max = $l;
                    $maxI = $mi;
                }
            }
            $m = $m[$maxI];
        }
        elseif (count ($m) == 1) {
            $m = $m[0];
        }
        
        if ($nm <= 0)
            return false;
        elseif (isset ($m[1]) && is_numeric($m[1]) && $m[1] > 0) {
            $val = false;
            if (empty ($m[2])) {
                // just a number
                $val = $m[1];
            }
            elseif (empty ($m[5]) || !is_numeric ($m[5]))
                return false;
            elseif (!empty ($m[3]) && !empty ($m[5]) && is_numeric ($m[5])) {
                // range
                $val = \ttkpl\cal::mean(array ($m[1],$m[5]));
            }
            elseif (!empty ($m[4])) {
                // plusminus
                $val = $m[1];
            }
            else
                return false;
            
            if ($converter !== null) {
                $oval = $val;
                $val = \ttkpl\scalarFactory::$converter ($val);
                $this->_addToStatus ("preparsed: $converter ($strHrDate = $oval) = $val");
            }
            else
                $this->_addToStatus ("preparsed: $strHrDate = $val");
            
            return $val;
        }
    }
    
    function tasks_available ($type = null) {
        $meths = get_class_methods($this);
        $o = array ();
        $fin = ($type === null) ? '[\w]+' : $type;
        foreach ($meths as $meth)
            if (preg_match ('/_task_(([\w_]+)_('.$fin.'))/i', $meth, $m) > 0)
                $o[] = ($type === null) ? $m[1] : $m[2];
        return (empty ($o)) ? false : $o;
    }
    
    function _task_thermal_age_csv_parser ($args) {
        $this->_addToStatus ("Parser: Thermal Age CSV");
        
        // cache a bunch of boring stuff
        $rK = array (); $rS = array ();
        
        // In order to easily work out how far through we are, assign some arbitrary fraction of 100% to parseing, processing and reporting
        
        
        // load csv file
        
        $orig_csv_path = @isset ($args['spreadsheet_csv']['Spreadsheet']['filename']) ? $args['spreadsheet_csv']['Spreadsheet']['filename'] : false;
        if (file_exists($orig_csv_path)) {
            $this->_addToStatus(basename($orig_csv_path) . " exists. Trying to open it...");
            $cp = new \ttkpl\csvData($orig_csv_path, TRUE);
            $this->_addToStatus("Headers found: " . implode ("|", $cp->titles));
            
            // Columns which might be missing in place of "human readable" alternatives which me must parse into these "proper" cols
            $addCols = array ('Year Analysed (AD)', 'Year Deposited (b.p.)');
            foreach ($addCols as $cn)
                if ($cp->addColumn($cn) !== false)
                    $this->_addToStatus ("Added missing column '$cn' to sheet.");
            
            
            // slug  (-and then detect headers (not all are required)-)
            $s2e = array ();
            foreach ($cp->titles as $title)
                $s2e[strtolower (Inflector::slug(str_replace (".","",$title)))] = $title;
            
            
            //$this->_addToStatus(print_r ($s2e, true));

            // count soil layer col sets
            $sKeys = array_keys ($s2e);
            $SLCnum = 0;
            while (in_array ('soil_type_' . ($SLCnum + 1), $sKeys))
                $SLCnum++;
            $this->_addToStatus("Max soil layers: " . $SLCnum);

            //$cp->getColumn($s2e[$slug]);

            $unParsed = array ();
            $parsed = array ();
            $xref = array (/*
                cache key => 
            */);
            $rowCount = 0; // heading
            
            
            $n = count ($cp->data);
            $nRowSamples = \ttkpl\temporothermal::aCSRangeDivisor * $n;
            if ($nRowSamples > $this->criticalRowSampleThreshold) {
                // This spreadsheet is too big to do all at once
                $this->_addToStatus('This spreadsheet is too big to process in one go. Will process in multiple batches.');
                $rowsDonePreviously = 0;
                
                $nBatches = ceil($nRowSamples / $this->criticalRowSampleThreshold);
                $nPerBatch = round ($n / $nBatches);
                if ($nPerBatch < 1) $nPerBatch = 1;
                if ($nBatches < 1) $nBatches = 1;
                
                // Do we know this already
                if (isset ($args['resume']) && is_array($args['resume'])) {
                    // yes
                    if (isset ($args['resume']['rowsParsed']))
                        $rowsDonePreviously = $args['resume']['rowsParsed'];
                    for ($i = 0; $i < $rowsDonePreviously; $i++) {
                        $cp->next();
                        $rowCount++;
                    }
                }
                else {
                    $args['resume'] = array (
                        'rowsParsed' => 0,
                        'rowsReported' => 0,
                        'nBatches' => $nBatches,
                        'nPerBatch' => $nPerBatch,
                        'nRows' => $n,
                    );
                }
                
                
                $this->_addToStatus("Calculating in $nBatches batches of $nPerBatch rows. Done $rowsDonePreviously rows already.");
            }
            // Work out how many percents we get for each parse, process and report
            $tfps = array_keys ($this->percentRatio);
            $this->percentsPer = array ();
            foreach ($tfps as $tfp) {
                $this->percentsPer[$tfp] = (100.0 / $n) * $this->percentRatio[$tfp];
            }
            //$this->_addToStatus("Work type time weightings: " . print_r ($this->percentRatio,true));
            //$this->_addToStatus("Percents per work type: " . print_r ($this->percentsPer,true));
            
            //$cp->next();
            $dontStop = true;
            do {
                $row = $cp->current();
                if (!$row) continue;
                $rowCount++;
                $sid = $row[$cp->getColumn($s2e['specimen_id'])];
                $me = array ();
                //print_r ($row);
                
                
                // find any of our "parse me more first" columns and parse them first
                $this->_addToStatus("Pre-parsing any human-readable columns...");
                // (for each new val, need to set $row for following logic and setColumn to save back to sheet
                if (isset ($s2e['hr_ya_ad']) && !empty ($row[$cp->getColumn($s2e['hr_ya_ad'])]) && empty ($row[$cp->getColumn($s2e['year_analysed_ad'])])) {
                    $row[$cp->getColumn($s2e['year_analysed_ad'])] = $this->_hrToOneDate ($row[$cp->getColumn($s2e['hr_ya_ad'])], 'ad');
                    $cp->setColumn($s2e['year_analysed_ad'], $row[$cp->getColumn($s2e['year_analysed_ad'])]);
                }
                if (isset ($s2e['hr_yd_bp']) && !empty ($row[$cp->getColumn($s2e['hr_yd_bp'])]) && empty ($row[$cp->getColumn($s2e['year_deposited_bp'])])) {
                    $row[$cp->getColumn($s2e['year_deposited_bp'])] = $this->_hrToOneDate ($row[$cp->getColumn($s2e['hr_yd_bp'])], '(?:cal)?\s*BC', 'bc2bp');
                    $cp->setColumn($s2e['year_deposited_bp'], $row[$cp->getColumn($s2e['year_deposited_bp'])]);
                }
                
                
                
                $this->_addToStatus("Finished pre-parsing.");

                
                if (empty ($row)) {
                    // empty row, do nothing
                    $this->_addToStatus("Empty Row " . $rowCount);
                }
                elseif (empty ($sid)) {
                    $this->_addToStatus("No ID in row " . $rowCount . "");
                    $row[$cp->getColumn($s2e['specimen_id'])] = 'noid-' . $rowCount;
                }

                if (!empty ($row)) {
                    //print_r ($s2e); die();
                    $me = array (
                        'burial' => array (
                            'Temporothermal' => array (),
                            'Burial' => array ()
                        ),
                        'specimen' => array (
                            'name' => @$row[$cp->getColumn($s2e['specimen_name'])],
                            'code' => @$row[$cp->getColumn($s2e['specimen_id'])],
                        ),
                    );
                    if ($row[$cp->getColumn($s2e['kinetics_id'])] > 0) {
                        $r = $this->_getReaction($row[$cp->getColumn($s2e['kinetics_id'])]);
                        if ($r) {
                            $me['reaction'] = $r;
                            $me['reaction']['Reaction']['reaction_id'] = $r['Reaction']['id'];
                        }
                    }
                    elseif (isset ($s2e['energy_of_activation_kj_mol']) && isset ($s2e['pre_exponential_factor_s'])) {
                        $me['reaction'] = array (
                            'Reaction' => array (
                                'reaction_id' => $row[$cp->getColumn($s2e['kinetics_id'])],
                                'ea_kj_per_mol' => $row[$cp->getColumn($s2e['energy_of_activation_kj_mol'])],
                                'f_sec' => $row[$cp->getColumn($s2e['pre_exponential_factor_s'])],
                                'name' => $row[$cp->getColumn($s2e['kinetics_name'])]
                            )
                        );
                    }
                    
                    // deposition date
                    $me['specimen']['Temporothermal']['stopdate_ybp'] = $row[$cp->getColumn($s2e['year_deposited_bp'])];
                    
                    $me['burial']['Burial']['numLayers'] = 0;
                    if (!(isset ($row[$cp->getColumn($s2e['latitude_decimal'])]) && isset ($row[$cp->getColumn($s2e['longitude_decimal'])]) &&
                         $row[$cp->getColumn($s2e['latitude_decimal'])] > -90 && $row[$cp->getColumn($s2e['latitude_decimal'])] < 90 &&
                         $row[$cp->getColumn($s2e['longitude_decimal'])] >= -180 && $row[$cp->getColumn($s2e['longitude_decimal'])] <= 180)) {
                        // invalid latlon
                        $this->_addToStatus("Bad lat/lon or dates in row " . $rowCount . " - skipping");

                    }
                    else {
                        // latlon are sane

                        // site
                        $me['site']['Site'] = array (
                            'name' => $row[$cp->getColumn($s2e['site_name'])],
                            'lon_dec' => $row[$cp->getColumn($s2e['longitude_decimal'])],
                            'lat_dec' => $row[$cp->getColumn($s2e['latitude_decimal'])],
                            'lapse_correct' => 1,
                        );
                        
                        // cond elev
                        if (isset ($s2e['elevation_wgs84']) && !empty ($row[$cp->getColumn($s2e['elevation_wgs84'])]) && is_numeric($row[$cp->getColumn($s2e['elevation_wgs84'])])) {
                            $me['site']['Site']['elevation'] = $row[$cp->getColumn($s2e['elevation_wgs84'])];
                            $this->_addToStatus("Add elev m: " . $row[$cp->getColumn($s2e['elevation_wgs84'])]);
                            $me['site']['Site']['elevation_source'] = "(user supplied in spreadsheet)";
                        }
                        
                        // process burial layers if any
                        for ($ssln = 1; $ssln <= $SLCnum; $ssln++) {
                            if (!isset ($me['burial']['SoilTemporothermal'])) $me['burial']['SoilTemporothermal'] = array ();
                            if (isset ($row[$cp->getColumn($s2e['thickness_m_' . $ssln])]) && $row[$cp->getColumn($s2e['thickness_m_' . $ssln])] > 0) {
                                
                                // layer is probably set
                                $dbSoil = $this->Soil->findById ($row[$cp->getColumn($s2e['soil_id_' . $ssln])]);
                                $layr = false;
                                //$this->_addToStatus("Loading soil $ssln");
                                // thermal diffusivity is set; along with length this is all we actually /need/
                                //$this->_addToStatus(print_r ($dbSoil,1));
                                if (isset ($row[$cp->getColumn($s2e['thermal_diffusivity_m2_day_' . $ssln])]) && $row[$cp->getColumn($s2e['thermal_diffusivity_m2_day_' . $ssln])] > 0) {
                                    $layr = array (
                                        'soil_id' => '-1',//$row[$cp->getColumn($s2e['soil_id_' . $ssln])],
                                        'name' => $row[$cp->getColumn($s2e['soil_type_' . $ssln])],
                                        'thickness_m' => $row[$cp->getColumn($s2e['thickness_m_' . $ssln])],
                                        'thermal_diffusivity_m2_day' => $row[$cp->getColumn($s2e['thermal_diffusivity_m2_day_' . $ssln])],
                                    );
                                }
                                elseif (!empty ($dbSoil)) {
                                    // They didn't specify thermal diffusivity but did specify an ID from the database
                                    $layr = array (
                                        'soil_id' => $dbSoil['Soil']['id'],
                                        'name' => $dbSoil['Soil']['name'],
                                        'thickness_m' => $row[$cp->getColumn($s2e['thickness_m_' . $ssln])],
                                        'thermal_diffusivity_m2_day' => $dbSoil['Soil']['thermal_diffusivity_m2_day'],
                                    );
                                }

                                if (!empty ($dbSoil) && $layr['thermal_diffusivity_m2_day'] != $dbSoil['Soil']['thermal_diffusivity_m2_day']) {
                                    // thermal diffusivity in spreadsheet and database don't match - remove id from col to prevent ambiguity
                                    $layr['soil_id'] = '';
                                    $cp->setColumn('soil_id_' . $ssln, '');
                                    $cp->setColumn('soil_type_' . $ssln, $row[$cp->getColumn($s2e['soil_type_' . $ssln])] . ' (Modified)');
                                }
                                else {
                                    $layr['soil_id'] = $dbSoil['Soil']['id'];
                                }
                                if (!!$layr) {
                                    $me['burial']['SoilTemporothermal'][] = $layr;
                                    $me['burial']['Burial']['numLayers']++;
                                }
                                
                            }
                        }
                        
                        $me['burial']['Temporothermal']['stopdate_ybp'] =  ($row[$cp->getColumn($s2e['year_deposited_bp'])]);
                        if (isset ($s2e['year_excavated_ad']) && isset ($row[$cp->getColumn($s2e['year_excavated_ad'])]) && $row[$cp->getColumn($s2e['year_excavated_ad'])] > 0) {
                            // storage
                            $me['burial']['Temporothermal']['startdate_ybp'] = ttkpl\scalarFactory::ad2bp ($row[$cp->getColumn($s2e['year_excavated_ad'])]);

                            $me['storage']['Temporothermal']['startdate_ybp'] = ttkpl\scalarFactory::ad2bp ($row[$cp->getColumn($s2e['year_analysed_ad'])]);
                            $me['storage']['Temporothermal']['stopdate_ybp'] = ttkpl\scalarFactory::ad2bp ($row[$cp->getColumn($s2e['year_excavated_ad'])]);
                            $me['storage']['Temporothermal']['temp_mean_c'] = $row[$cp->getColumn($s2e['mean_temp_deg_c'])];
                            $me['storage']['Temporothermal']['temp_pp_amp_c'] = $row[$cp->getColumn($s2e['temp_range_tmax_tmin_deg_k'])];
                        }
                        else {
                            $me['burial']['Temporothermal']['startdate_ybp'] =  ttkpl\scalarFactory::ad2bp ($row[$cp->getColumn($s2e['year_analysed_ad'])]);
                            // @TODO Fix so no need for this
                            // Setting time range of 0 prevents running storage temporothermal and pointless error
                            $me['storage']['Temporothermal']['startdate_ybp'] = 0;
                            $me['storage']['Temporothermal']['stopdate_ybp'] = 0;
                        }

                        
                    }
                    //print_r ($me); die();
                    

                    

                    //print_r ($me);

                }
                if (!empty ($me)) {
                    // Generate a unique "fingerprint". If these match we won't recalculate the row
                    $ck = preg_replace('/[^0-9.:-]/','',serialize($this->_stripVerbal($me)));
                    if (!isset ($xref[$ck])) {
                        $xref[$ck] = array ();
                    }
                    $unParsed[$cp->key()] = $me;
                    $unParsed[$cp->key()]['fingerprint'] = $ck;
                    $xref[$ck][] = $cp->key();
                    
                }
                else
                    $this->_addToStatus ("Nothing to do for this row " . $rowCount);
                //$this->_addToStatus("<pre>".serialize ($parse)."</pre>");
                //die();
                if (isset ($args['resume']) && isset ($args['resume']['rowsParsed'])) {
                    $args['resume']['rowsParsed']++;
                    $dontStop = ($rowCount <= $rowsDonePreviously + $nPerBatch) ? true : false;
                }
                $this->increaseJobPercentComplete('parse');
            } while ($cp->next() && !!$dontStop);
            
            //$cp->export($orig_csv_path);
            
            foreach ($unParsed as $rowkey => $up) {
                if ($xref[$up['fingerprint']][0] == $rowkey) {
                    $parse = $this->_task_dna_screener_parser ($up);
                    $parsed[$rowkey] = $parse;
                    $pl = (count ($xref[$up['fingerprint']]) > 1) ? 's' : '';
                    $this->_addToStatus ("Parsed row{$pl} " . implode(" ", array_map (function ($a) { return $a+1; }, $xref[$up['fingerprint']])));
                }
            }
            
            $rtn = array (
                'resume' => (isset ($args['resume'])) ? $args['resume'] : false,
                'unParsed' => $unParsed,
                'xref' => $xref,
                'parsed' => $parsed,
                'spreadsheet_csv' => $args['spreadsheet_csv']
            );
            //file_put_contents (APP . DS . "csvdbg.txt", serialize ($this->cleanse($parsed), true));
            return $rtn;
        }
        return false;


    }
    
    function _stripVerbal ($arrIn) {
        $arrOut = array ();
        foreach ($arrIn as $k => $v) {
            if (in_array ($k, array ('User', 'Citation')))
                continue;
            elseif (is_array ($v))
                $arrOut[$k] = $this->_stripVerbal ($v);
            elseif (is_numeric($v))
                $arrOut[$k] = $v;
        }
        return $arrOut;
    }

    /**
     * ignore this if you like, it's just to make the graph render faster in svg by plotting points
     * decresingly often as we approach longer lengths (log graph innit)
     */
    function _unprecision ($x, $precision = 5) {
        $n = ceil ($x / $precision);
        return ($n < 1) ? 1 : $n;
    }
    function Ps ($length, $λ) { // probability that a DNA strand of a given length will survive
        return pow (1 - $λ, $length - 1);
    }
    
    function _generate_latex_pdf ($arrOpts = null) {
        
        $this->_addToStatus("Generating PDF file...");
        
        if (!is_array ($arrOpts)) $arrOpts = array ();
        $defaults = array (
            'filename' => 'report',
            'latex_runs' => 4
        );
        $options = Set::merge ($defaults, $arrOpts);
        
        // Generate LaTeX source
        if (!is_callable ($this->fnRenderer)) {
            $this->_addToStatus("ERROR: Renderer closure not set!");
            return array ('error' => 'Renderer closure not set.');
        }
        $fnR = $this->fnRenderer;
        
        // get paths
        $tmpDir = $this->_makeJobTmpDir() . DS;
        if ($tmpDir == false) return array ('error' => 'Couldn\'t create/access tmp folder');
        $pkgDir = APP.'vendors'.DS.'latex'.DS;
        $rptDir = APP.'webroot/reports/';
        $baseFile = sprintf ('%s%s.',$tmpDir,$options['filename']);
        $texFile = $baseFile . 'tex';
        $pdfFile = $baseFile . 'pdf';
        $reportGraphBasepath = APP.WEBROOT_DIR.DS;
        
        // send rgb to view
        //$options['data']['rgbPath'] = $reportGraphBasepath;
        
        // Render LaTeX view
        $latex = $fnR ($options);
        
        // symlink latex pkg deps to tmp folder
        $pkgs = scandir($pkgDir);
        $exclude = array ('.','..');
        foreach ($pkgs as $pkg)
            if (!in_array ($pkg, $exclude) && !is_dir($pkg) && !file_exists($tmpDir.$pkg))
                symlink ($pkgDir.$pkg, $tmpDir.$pkg);
        // symlink report images to tmp folder
        $allowed = implode ('|',array ('png','pdf','eps','tex','svg'));
        $rpts = array_filter (scandir($rptDir), function ($v) use ($allowed, $options) {
            if (strpos($v, $options['job_id']) !== 0) return false;
            if (preg_match ('/\.('.$allowed.')$/i', $v) > 0) return true;
            return false;
        });
        foreach ($rpts as $rpt)
            if (!is_dir($rpt) && !file_exists($tmpDir.$rpt))
                symlink ($rptDir.$rpt, $tmpDir.$rpt);
        
        // output tex to file
        file_put_contents ($texFile, $latex);
        
        while ($options['latex_runs'] > 0) {
            $last_run_output = shell_exec("cd \"$tmpDir\" && pdflatex \"$texFile\"");
            if (!file_exists ($pdfFile)) {
                $this->_addToStatus("Error generating PDF report: " . $last_run_output);
                return array ('error' => 'pdflatex failed', 'details' => $last_run_output);
            }
            $options['latex_runs']--;
        }
        
        return array ('pdf_filename' => $pdfFile);
        
        
        // Return array ( 'pdf_filename' => $pdfFile | 'error' = "error msg" )
    }
    
    function _generate_dna_screener_pdf ($job_id = null) {
        if ($job_id === null) $job_id = $this->id;
        $job = $this->read (null, $job_id);
        $job['Job']['data'] = unserialize ($job['Job']['data']);
        $report = unserialize ($this->_bgpGetJobFileContent ('report', $job_id));
        
        $defaults = array (
            'job_id' => $job_id,
            'filename' => 'S'.$job_id.'_dna_screening_report',
            'action' => 'latex/report.tex',
            'data' => array (
                'title' => 'TITLE NOT SET',
                'author' => 'AUTHOR NOT SET',
                'keywords' => 'thermal-age.eu, DNA, Collagen, thermal age, depurination, curation, ancient, bone',
                'job' => $job,
                'report' => $report
            ),
        );
        
        $options = Set::merge ($defaults, compact ('data'));
        
        return $this->_generate_latex_pdf ($options);
        
    }
    function _generate_csv_pdf ($job_id) {
        
        if ($job_id === null) $job_id = $this->id;
        $job = $this->read (null, $job_id);
        $job['Job']['data'] = unserialize ($job['Job']['data']);
        $report = unserialize ($this->Job->_bgpGetJobFileContent ('report', $job_id));
        
        $defaults = array (
            'job_id' => $job_id,
            'filename' => 'B'.$job_id.'_dna_screening_batch_report',
            'action' => 'latex/report_multi.tex',
            'data' => array (
                'title' => 'MY CSV TITLE',
                'author' => 'AM CSV AUTHOR',
                'keywords' => 'DNA, Collagen, thermal age, depurination, curation, ancient, bone',
                'job' => $job,
                'report' => $report
            ),
        );
        
        $options = Set::merge ($defaults, compact ('data'));
        
        return $this->_generate_latex_pdf ($options);
        
    }
    
    var $fnRenderer = null;
    /**
     * Store view renderer closure
     * @param function $fnRenderer
     * @return boolean true if $fnRenderer is_callable and has been set
     */
    function _set_renderer_closure ($fnRenderer) {
        if (is_callable($fnRenderer))
            $this->fnRenderer = $fnRenderer;
        else
            return false;
        return true;
    }
    
    function _task_dna_screener_reporter ($args) {
        $this->_addToStatus ("Reporter: DNA Screener");
        global $tempDir;
        $tempDir = $this->_makeJobTmpDir() . "/";
        
        $ta = $args['thermalAge'];
        $taYrs = $args['thermalYears'];
        $results = array (
            'summary' => array (
                'λ' => $ta->getLambda(),
                '(1/λ)+1' => 1 + (1 / $ta->getLambda()),
                'k (yr)' => $ta->getKYear (),
                'k (sec)' => $ta->getKSec (),
                'Teff' => \ttkpl\scalarFactory::makeCentigradeAbs ($ta->getTeff ())->getValue(),
                'Thermal age' => $taYrs->getValue(),
            ),
        );

        // get thermal age object - full of juicy data om nom nom!
        $tao = $args['objects'][0]; // thermal age object
 
        // log processing speed:
        $ttStats = array ();
        foreach ($tao->temporothermals as $ttInd => $tt) {
            if (!empty ($tt->twData['spl_yrs_sec'])) {
                $numSpls = floor ($tt->rangeYrs / $tt->chunkSize);
                $splsPerSec = 1 / $tt->twData['sec_spl_yr'];
                $ttStats[] = sprintf ("%d sample yrs at %01.5f sec/spl (%01.3f spl/sec or ~%3.1f sec total)", $numSpls, $tt->twData['sec_spl_yr'], $numSpls * $tt->twData['sec_spl_yr']);
            }
        }
        if (!empty ($ttStats)) {
            $message = sprintf ("Performance: %s", implode (", ", $ttStats));
            $this->_addToStatus($message);
            $results['summary']['performance'] = $message;
        }


        // DRAW GRAPHS
        $results['graphs'] = array ();

        $this->_addToStatus ("Drawing lambda graph...");
        $results['graphs']['lambda'] = $this->_draw_lambda_graph(array (
            'lambda' => $results['summary']['λ'],
            'file_id' => $this->field ('id')
        ));


        // draw graphs of each temporothermal
        $this->_addToStatus ("Drawing temporothermal graph(s)...");
        foreach ($tao->temporothermals as $ttInd => $tt) {
            if (!$tt->constantClimate) {
                $results['graphs']['burial'] = $this->_draw_temporothermal_history_graph (array (
                    'temporothermal' => $tt,
                    'filename_base' => "temporothermal_{$ttInd}_graph",
                    'histogram' => $tao->histograms[$ttInd],
                    'file_id' => $this->field ('id')
                ));
            }
        }


/*
        $anonLineColours = array (
            '990033',
            '332222',
            '7f9900',
            '009966',
            '190099',
        );
        $plot = new \ttkpl\ttkplPlot("Fragment Length Distribution (with examples for comparison)", 1, 1, "850,520");
        
        $plot->labelAxes("DNA Fragment Length", "Relative Probability of survival through not-being-depurinated")
                ->setGrid(array ('x','y'))
                ->setLog(array ('x'));
        $mfl = 100;
        $pl = 0;
        $labels = array_keys ($lambdas);
        foreach (array_values($lambdas) as $li => $λ) {
            if ($li == 0) { // The REAL result
                $pl = $λ;
                $plot->setData (sprintf ("λ = %0.6f", $λ), $li+2, 'x1y1', 'line', '1:2');
                $mfl = round ((1/$λ)+1);
                $plot->setData ("Mean Fragment Length = $mfl", 1, 'x1y1', 'points pointsize 3')
                     ->addData ($mfl, $this->Ps ($mfl, $λ), 1);
            }
            else { // Example results for context
                if (abs ($pl - $λ) > 0.002) { // If the line is not too close to the real line
                    if ($λ > 1) $λ = 1;
                    $plot->setData ("(λ of " . $labels[$li] . ")", $li+2, 'x1y1', 'line linecolor rgbcolor "#' . $anonLineColours[$li - 1] . '"', '1:2');//, 'notitle');
                }
            }

            for ($l = 0; $l <= $mfl * 10; $l += $this->_unprecision($l)) {
                //print_r (array ($l, $this->Ps ($l, $λ), 2));
                $plot->addData ($l, $this->Ps ($l, $λ), $li+2);
            }
        }

        $n = "reports/lambdas_fragment_lengths_" . $this->field ('id') . ".svg";
        $fn = WWW_ROOT . $n;
        $this->_addToStatus("Saving lambda graph to $fn");
        
        $plot->plot($fn);*/

        

        $report = $this->bgpGetJobFileName('report');
        $this->_addToStatus("Saving report to $report");
        file_put_contents($report, serialize ($results));
        if (1) {
            //$debug = $this->bgpGetJobFileName('debug');
            
            //$this->_addToStatus("Cleansing debug info");
            
            //$dbg = $args['objects'];
            //$dbg = $this->cleanse ($dbg);

            //$this->_addToStatus("Saving debug to $debug");
            //file_put_contents($debug, print_r ($dbg, true));
        }



        $this->_clearJobTmpDir();
    }

    /**
     * This most definitely needs refactoring into ttkpl at some point!
     * @param array $arrOpts string indexed array of options to overide the defaults
     */
    function _draw_temporothermal_history_graph ($arrOpts = null) {
        $opts = array_merge ($this->_graphDefaults(), array (
            'filename_base' => 'temporothermal_graph',
            'temporothermal' => null,
            'histogram' => null
        ), $arrOpts);

        // WARNING: FOLLOWING LIFTED WHOLESALE FROM DEV WORK AND IS PROBABLY INCONSISTENT




        $plot = new \ttkpl\GNUPlot();
        //$plot->reset();

        // $plot->set ("size 2.5/5.0, 2.5/3.5");
        // $plot->set ("origin 0.5/5.0, 0.5/3.5");


        $plot->setSize( 1.0, 1.0 );

        // $plot->set ("tmargin 0");
        //$plot->set ("rmargin 10");
        //$plot->set ("bmargin 30");
        // $plot->set ("lmargin 0");

        $tt = '';
        $tt .= "\\n";
        $tt .= $opts['histogram']->numPoints . " nominal days sampled at " . $opts['temporothermal']->chunkSize . "yr intervals over " . $opts['temporothermal']->stopDate->getYearsBp() . " to " . $opts['temporothermal']->startDate->getYearsBp() . "yrs. b.p.\\n";
        $mrsp = round ($opts['temporothermal']->meanCorrection->source[1]->regRSqPc (), 2);
        $tt .= "T at ".$opts['temporothermal']->location."/K = " . round ($opts['temporothermal']->meanCorrection->a, 2) . " * T(global anom.) + " . round ($opts['temporothermal']->meanCorrection->offset->a, 2) . " ($mrsp%)\\n";
        $arsp = round ($opts['temporothermal']->ampCorrection->source[1]->regRSqPc (), 2);
        $tt .= "A(p-p) at ".$opts['temporothermal']->location."/K = " . round ($opts['temporothermal']->ampCorrection->a, 2) . " * T(global anom.) + " . round ($opts['temporothermal']->ampCorrection->offset->a, 2) . " ($arsp%)\\n";
        if (!empty ($opts['temporothermal']->burial)) $tt .= "Burial(z,Dh): " . $opts['temporothermal']->burial;

        $plot->setTitle($tt);

        $plot->set ("autoscale");
        //$plot->set ("log y");
        //$plot->set ("log y2");
        //$plot->set ("xtics rotate by 330");
        $plot->set ("nolog y");
        $plot->setTics ("y", 'nomirror');
        $plot->setTics ("x", 'nomirror');
        $plot->setTics ("y2", 'nomirror');
        $plot->setTics ("x2", 'nomirror');
        $plot->set ("grid noy2tics ytics");
        $plot->set ("grid nox2tics xtics");
        $plot->set ('border 3 "black"');

        $plot->setDimLabel ("x2", "Bin temperature/C");
        $plot->setDimLabel ("y2", "# days at bin temperature");
        $plot->setDimLabel ("x", "Years b.p.");
        $plot->setDimLabel ("y", "Absolute or relative temperature/C at time");
        $plot->set ("key left below");
        // $plot->set ("key box");
        $plot->set ("size ratio 0.5");


        $deq = "# days";
        $dH = new \ttkpl\PGData($deq);
        foreach ($opts['histogram']->bins as $bi => $bc) {
            $dH->addDataEntry( array(($opts['histogram']->labels[$bi] + \ttkpl\scalarFactory::kelvinOffset), $bc) );
        }
        if (isset($opts['temporothermal']->twData['TGraph'])) {
            $dLS = new \ttkpl\PGData("Local temp range");
            $dLB = new \ttkpl\PGData("Local temp range (buried)");
        }
        if (isset($opts['temporothermal']->twData['teff'])) {
            $dT = new \ttkpl\PGData("Effective Temperature");
        }
        $dM = new \ttkpl\PGData("Local mean (abs)");
        $da = new \ttkpl\PGData("Local ± amplitude (rel)");
        $dga = new \ttkpl\PGData("Mean global anomaly (rel, 0bp base)");
        //die ("\nCT: " . print_r ($opts['temporothermal']->twData, true));
        foreach ($opts['temporothermal']->twData['mean'] as $years => $mat) {
            if (isset($opts['temporothermal']->twData['TGraph'])) {
                $dLS->addDataEntry($opts['temporothermal']->twData['TGraph']['surface'][$years]);
                $dLB->addDataEntry($opts['temporothermal']->twData['TGraph']['buried'][$years]);
            }
            if (isset($opts['temporothermal']->twData['teff'])) {
                $dT->addDataEntry(array ($years, $opts['temporothermal']->twData['teff'][$years]));
            }
            $dM->addDataEntry( array($years, $mat));
            $da->addDataEntry( array($years, $opts['temporothermal']->twData['amp'][$years]/2) );
            $ypt = new \ttkpl\palaeoTime ($years);
            $dga->addDataEntry( array($years, $opts['temporothermal']->temperatures->getGlobalMeanAnomalyAt($ypt)->getScalar()->getValue() ));

        }
        $plot->plotData( $dH, 'boxes', '1:2', 'x2y2', 'fs solid 0.5 lc rgb "#999999"');
        if (isset($opts['temporothermal']->twData['TGraph'])) {
            $plot->set ("style fill solid 0.1 noborder");
            $plot->plotData( $dLS, 'filledcu', '1:2:3', 'x1y1');
            $plot->plotData( $dLB, 'filledcu', '1:2:3', 'x1y1');
        }
        //$plot->plotData( $dM, 'lines', '1:2', 'x1y1', 'smooth bezier');
        $plot->plotData( $dM, 'lines', '1:2', 'x1y1', 'lw 2');
        $plot->plotData( $da, 'lines', '1:2', 'x1y1', 'lw 1');
        $plot->plotData( $dga, 'lines', '1:2', 'x1y1', 'lw 1'); // */// <-- fix me!

        if (isset($opts['temporothermal']->twData['teff'])) {
            $plot->plotData( $dT, 'lines', '1:2', 'x1y1', 'lw 2');
        }

        $plot->setRange('x', $opts['temporothermal']->startDate->getYearsBp(), $opts['temporothermal']->stopDate->getYearsBp());

        //$plot->set ("size ratio 0.5");
        
        //$plot->export('thermal_age_test.png');

        //$plot->close();

        // BADNESS ENDS.

        $nbase = $opts['web_uri_path'] . sprintf ("%s_%s", $opts['file_id'], $opts['filename_base']);
        $n = $nbase . '.' . $opts['file_ext'];
        $fn = $opts['webroot'] . $nbase;
        
        $this->_addToStatus("Saving temporothermal graph to $n");
        
        \ttkpl\ttkplPlot::__export ($plot, $fn, $opts['all_ext']);
        
        $plot->close();

        return (file_exists ($fn . '.' . $opts['file_ext'])) ? $n : false;

    }

    /**
     * This most definitely needs refactoring into ttkpl at some point!
     * @param array $arrOpts string indexed array of options to overide the defaults
     */
    function _draw_lambda_graph ($arrOpts = null) {
        $opts = array_merge ($this->_graphDefaults(), array (
            'filename_base' => 'lambdas_fragment_lengths',
            'lambda' => 1, // default to total destruction
            'example_lambdas' => array (
                "Complete Destruction" => 1,
                "Ötzi" => 0.001893,
            ),
            'show_examples' => true
        ), $arrOpts);
        $opts['lambdas'] = array_merge (array ("" => $opts['lambda']), $opts['example_lambdas']);
        
        $plot = new \ttkpl\ttkplPlot("Fragment Length Distribution (with examples for comparison)", 1, 1, "850,520");

        $plot->labelAxes("DNA Fragment Length", "Relative Probability of survival through not-being-depurinated")
                ->setGrid(array ('x','y'))
                ->setLog(array ('x'));
        $mfl = 100;
        $pl = 0;
        $labels = array_keys ($opts['lambdas']);
        foreach (array_values($opts['lambdas']) as $li => $λ) {
            if ($li == 0) { // The REAL result
                $pl = $λ;
                $plot->setData (sprintf ("λ = %0.6f", $λ), $li+2, 'x1y1', 'line', '1:2');
                $mfl = round ((1/$λ)+1);
                $plot->setData ("Mean Fragment Length = $mfl", 1, 'x1y1', 'points pointsize 3')
                     ->addData ($mfl, $this->Ps ($mfl, $λ), 1);
            }
            elseif (!!$opts['show_examples']) { // Example results for context
                if (abs ($pl - $λ) > 0.002) { // If the line is not too close to the real line
                    if ($λ > 1) $λ = 1;
                    $plot->setData ("(λ of " . $labels[$li] . ")", $li+2, 'x1y1', 'line linecolor rgbcolor "#' . $opts['colours'][$li - 1] . '"', '1:2');//, 'notitle');
                }
            }

            for ($l = 0; $l <= $mfl * 10; $l += $this->_unprecision($l)) {
                //print_r (array ($l, $this->Ps ($l, $λ), 2));
                $plot->addData ($l, $this->Ps ($l, $λ), $li+2);
            }
        }
        $nbase = $opts['web_uri_path'] . sprintf ("%s_%s", $opts['file_id'], $opts['filename_base']);
        $n = $nbase . '.' . $opts['file_ext'];
        $fn = $opts['webroot'] . $nbase;
        
        $this->_addToStatus("Saving lambda graph to $n");
        $plot->plot($fn, $opts['all_ext']);
        
        return (file_exists ($fn . '.' . $opts['file_ext'])) ? $n : false;

    }

    function _graphDefaults () {
        return array(
            'web_uri_path' => 'reports/', // with trailing/ not /leading slash <-- bad nerd poetry?
            'filename_base' => 'user_graph',
            'file_ext' => 'svg', // this is returned for use somewhere
            'all_ext' => array ('png', 'svg', 'pdf'), // these are those which are actually generated (should include above!)
            'file_id' => microtime (1),
            'webroot' => WWW_ROOT,
            'colours' => array (
                '990033',
                //'332222',
                '7f9900',
                '009966',
                '190099',
            )
        );
    }

    /** Filters massive arrays of data out of objects being dumped during debugging/logging
     *
     * @param <type> $arrIn
     * @param <type> $maxN
     * @param <type> $maxL
     * @param <type> $l
     * @return <type>
     */
    function cleanse ($arrIn, $maxN = 1000, $maxL = 6, $l = 0) {
        if (!\is_object($arrIn) && !\is_array($arrIn)) return $arrIn;
        foreach ($arrIn as $i => &$c) {
            if (is_object($c)) {
                $d = array ();
                foreach ($c as $x => $y)
                    $d[$x] = $y;
                $c = $d;
            }
            if (is_array ($c)) {
                ++$l;
                if (count ($c) > $maxN || $l > $maxL)
                    unset ($arrIn[$i]);
                elseif ($l) {
                    $c = $this->cleanse ($c, $maxN, $maxL, $l);
                }
                $l--;
            }
        }

        return $arrIn;
    }



    /**
     * Creates runtime files etc.
     * @return bool success
     */
    function _startProcessing ($forceReload = false) {
        //die();
        $id = $this->field ('id');
        if ($id !== false) {

            App::import ('Vendor', 'ttkpl/lib/ttkpl');

            // Currently we only need to import models for stuff which will only have been input by
            // record ID during data capture.
            foreach (array ('Reaction', 'Soil') as $importModel)
                $this->$importModel = ClassRegistry::init ($importModel);

            foreach (array ('pid', 'status','percent') as $f)
                $this->bg[$f] = $this->bgpGetJobFileName($f);
            file_put_contents($this->bg['pid'], posix_getpid ());
            if (!file_exists($this->bg['status'])   || $forceReload === true)     file_put_contents($this->bg['status'],    '');
            if (!file_exists($this->bg['percent'])  || $forceReload === true)     file_put_contents($this->bg['percent'],   '0');
            $this->_addToStatus("Starting processor for job $id");
            $this->bg['startTime'] = microtime (true);

            $this->save (array ('Job' => array ('id' => $id, 'status' => 1)), false);

            return true;
        }
        return false;
    }
    /**
     * Cleans up runtime files and changes job status
     */
    function _stopProcessing ($error = false, $resume = false) {
        // if $resume is true then the job is only partially complete
        $id = $this->field ('id');
        if ($id !== false) {
            $this->bg['stopTime'] = microtime (true);
            $batchD = (!!$resume) ? "a batch of " : '';
            $this->_addToStatus("Finished {$batchD}job $id");
            $rtOf = (!!$resume) ? "Batch" : "Total";
            $this->_addToStatus("{$rtOf} runtime was " . ($this->bg['stopTime'] - $this->bg['startTime']));
            unlink ($this->bg['pid']);
            
            $status = 3; // default to error 0-o
            if ($error !== false) {
                $status = 3; // err
                $this->_addToStatus('Setting job status to: error (3)');
            }
            elseif ($resume !== false) {
                $status = 0; // pend
                $this->_addToStatus('Setting job status to: pending (is large job processed in multiple batches) (0)');
            }
            else {
                $status = 2; // fin
                $this->_addToStatus('Setting job status to: finished (2)');
            }
                
            $this->save (array ('Job' => array ('id' => $id, 'status' => $status)), false);

            return true;
        }
        return false;
    }
    /**
     * Adds a timestamped message to the TOP of the status file
     * @return bool false if status file doesn't exist or can't be written else true
     */
    function _addToStatus ($message) {
        $fmsg = sprintf("%f %s\n", microtime (true), $message);
        if (PHP_SAPI == 'cli') echo $fmsg;
        if (!empty ($this->bg) && !empty ($this->bg['status']) && file_exists ($this->bg['status'])) {
            // @TODO rewrite this to append to file properly rather than this abomination!
            $st = file_get_contents ($this->bg['status']);
            $st = $fmsg . $st;
            return file_put_contents ($this->bg['status'], $st);
        }
        return false;
    }
    /**
     * 
     * @param int $percent_complete save percentage completeness of this job id to status file
     * @return bool success
     */
    function _updateJobPercentComplete ($percent_complete = 0) {
        if (!empty ($this->bg) && !empty ($this->bg['percent']) && file_exists ($this->bg['percent'])) {
            return file_put_contents ($this->bg['percent'], $percent_complete);
        }
    }
    /**
     * 
     * @param type $increase_by_points number of % to increase by or array key of $this->percentsPer
     * @param type $id optional. Job ID. 
     * @return boolean
     */
    function increaseJobPercentComplete ($increase_by_points, $id = null) {
        $id = ($id === null) ? $this->id : $id;
        $tfps = array_keys ($this->percentRatio);
        if (in_array ($increase_by_points, $tfps)) {
            $increase_by_points = $this->percentsPer[$increase_by_points];
        }
        if (!$increase_by_points ||$increase_by_points == 0) $this->_addToStatus ("Increase by what points: ".$increase_by_points);
        //if ($increase_by_points == 0) die ($increase_by_points);

        $current = $this->getJobPercentComplete($id);
        if ($current != -1) {
            $this->_updateJobPercentComplete($current + $increase_by_points + 0.0);
            return true;
        }
        return false;
    }
    /**
     * @param int $id Job id (default: $this->id)
     * @return int -1 if not set else number from 0 to 100 if job is running (value not guaranteed to persist after job is complete)
     */
    function getJobPercentComplete ($id = null) {
        $id = ($id === null) ? $this->id : $id;
        $percent_complete = $this->_bgpGetJobFileContent('percent',$id);
        if ($percent_complete === false) return -1; // file doesn't exist
        //if ($percent_complete < 0) return 0;
        //if ($percent_complete > 100) return 100;
        return $percent_complete+0.0;
    }
    
    /**
     * Build percent complete update closure to drop into ttkpl in a nice clean abstract way
     */
    function _buildPCUClosure () {
        $this->pCUClosure = function ($percent_complete) {
            return $this->_updateJobPercentComplete($percent_complete);
        };
    }
    
    /**
     * Spawns a background process to run tryProcessNext
     */
    function _forkToBackground () {
        
        $guessRunning = $this->_bgpCountCakeBackgroundProcesses();
        $this->sleepyTime = 0.5 + ($guessRunning * .5);
        $this->_addToStatus("There may be up to {$guessRunning} threads running already. Waiting {$this->sleepyTime}s...");
        
        $command = "php -q " . APP . "../cake/console/cake.php --app " . APP . " background";
        $this->_addToStatus("Forking new worker thread now.");
        //die ("nohup $command > /dev/null 2>&1 & echo $!");
        $pid = shell_exec ("nohup $command > /dev/null 2>&1 & echo $!");
        $this->_addToStatus("Started with PID $pid");
        return ($pid);
    }
    /**
     * Run by tryProcessNext prior to doing any work; checks for jobs with status=running and a
     * process which is running (and not crashed) and compares against maxThreads
     * @return bool is it ok to create another bg processing thread
     */
    function _goodToGo () {
        $this->_addToStatus("G2G");
        // if running threads < maxThreads return true
        return ($this->_bgpCountRunningJobProcesses() >= $this->maxThreads) ? false : true;
    }

    function _bgpCountRunningJobProcesses () {
        $runningJobs = $this->_bgpGetRunningJobs();
        $numProcs = count ($runningJobs);
        foreach ($runningJobs as $j)
            if (!$this->bgpIsRunning($this->bgpGetPid($j['Job']['id'])))
                $numProcs--;
        return $numProcs;
    }
    
    /**
     * WARNING: This isn't watertight - it is a guideline only!
     */
    function _bgpCountCakeBackgroundProcesses () {
        return (int) exec('ps aux | grep -E "c[ak]{2}e(.*?)background(\W|$)" | wc -l');
    }

    /**
     * Attempts to get human readable status information about a job. Possible responses are:
     * - Pending (place in queue, time estimates)
     * - Running (latest output from status file/socket, % complete, time estimate)
     * - Finished (time to complete, link to report)
     * - Error (error info)
     *
     * @return array of info (probably to pass on to view)
     */
    function bgGetProgress () {
        $status = $this->field ('status');
        if ($status !== FALSE) {

            $rtn = array (
                'statusCode' => $status,
                'statusName' => $this->_bgGetStatusName($status),
            );

            switch ($status) {
                case 0: // Pending
                    $qp = $this->bgGetQueuePos();
                    if ($qp < 1)
                        $rtn['statusText'] = "Your job is up next!";
                    else
                        $rtn['statusText'] = sprintf ("There %s %d job%s ahead of yours in the queue...", ($qp>1)?'are':'is', $qp, ($qp>1)?'s':'');
                    break;
                case 1: // Running
                    $rtn['statusText'] = sprintf ("Your job is running now.");
                    break;

                case 2: // Complete
                    $rtn['statusText'] = sprintf ("Job is complete.");
                    break;

                case 3: // Error
                    $rtn['statusText'] = sprintf ("Job finished, but with errors.");
                    break;
            }
            
            return $rtn;
        }
        else return false;
    }

    function _bgGetStatusName ($statusCode = -1) {
        return (isset ($this->statusCodes[$statusCode])) ?
                $this->statusCodes[$statusCode] :
                sprintf ('%1d ' . 'Unknown', $statusCode);
    }
    /**
     * How many jobs are ahead of this one for processing?
     * @return int number of jobs ahead of this job in the queue with status < 2 (i.e. jobs which are pending or running)
     */
    function bgGetQueuePos () {
        $id = $this->field ('id');
        if ($id !== FALSE) {
            return $this->find('count', array (
                'conditions' => array (
                    'Job.id <' => $id,
                    'Job.status' => array (0, 1),
                )
            ));
        }
        else return false;
    }
    /**
     * @param int $since unix timestamp for log cutoff
     * @return array statuses of the process is running, should be running, has crashed
     */
    function bgpGetStatus ($since = null) {
        $since = ($since === null) ? time () : $since;
        $since = 1;
        $status = $this->field ('status');
        if ($status !== false) {

            $rtn = array (
                'statusCode' => $status,
                'statusName' => $this->_bgGetStatusName($status),
                'statusFile' => $this->bgpGetStatusFileSince ($since),
            );
            $dead = $this->bgpBOYD();
            if ($status == 1 && $dead) {
                $rtn['statusText'] = sprintf ("Uh oh, it looks like the job has crashed. Stand by for a status update!");
                $rtn['statusCode'] = 3;
                return $rtn;
            }
            else {
                return array_merge ($rtn, $this->bgGetProgress ());
            }
        }
        else {
            return false;
        }
    }
    /**
     *
     * @return mixed int pid if pidfile exists else null
     */
    function bgpGetPid ($job_id = null) {
        //$this->_addToStatus("Get PID for job $job_id");
        $pid = $this->_bgpGetJobFileContent('pid', $job_id);
        //$this->_addToStatus("...is $pid");
        return ($pid != false) ? sprintf ("%d", $pid) : false;
    }
    /**
     * Reads status file a line at a time from the top and stops once the timestamp
     * @param int $since unix timestamp to look for lines stamped after
     * @return mixed string of status file since timestamp or false if none available/no file
     */
    function bgpGetStatusFileSince ($since = null) {
        $since = ($since === null) ? 1 : $since;
        $fn = $this->bgpGetJobFileName ('status');
        if (!file_exists ($fn)) return false;
        return file_get_contents($fn);
        
        $handle = fopen ($fn, 'r');
        $op = '';
        $old = false;
        while (!feof ($handle) && !$old) {
            $line = fgets ($handle, 4096);
            if (preg_match ("/^(\d+?:(\.\d}))\s/", $line, $m) > 0) {
                $ts = (float) $m[1];
                if ($ts > $since)
                    $op .= $line;
                else
                    $old = true;
            }
        }
        return (strlen ($op) == 0) ? false : $op;
    }
    /**
     *
     * @param string $file extension of file e.g. pid, status, log etc.
     * @return string
     */
    function bgpGetJobFileName ($file = 'pid', $job_id = null) {
        $id = ($job_id === null) ? $this->field('id') : $job_id;
        if ($id !== FALSE) {
            $file = TMP . sprintf ("jobrun/job_%d.%s", $id, $file);
        }
        return $file;
    }
    /**
     *
     * @return mixed path to job's scratch folder if exists/created else false
     */
    function _makeJobTmpDir () {
        $id = $this->field('id');
        if ($id !== FALSE) {
            $dir = TMP . sprintf ("jobrun/job_%d", $id);
            if (!file_exists ($dir) && !is_dir($dir)) {
                if (!mkdir ($dir, 0777))
                    return false;
            }
            elseif (is_dir ($dir))
                return $dir;
            $thisDir = $dir;
            return $dir;
        }
        return false;
    }
    function _clearJobTmpDir () {
        //if (is_dir ($thisDir)) {
            //return rmdir($thisDir);
        //}
    }
    /**
     * Generic function for getting stuff out of one of a jobs possible files
     * @param string $file see bgpGetJobFile
     * @return string file content or false if doesn't exist
     */
    function _bgpGetJobFileContent ($file = 'pid', $job_id = null) {
        $fn = $this->bgpGetJobFileName($file, $job_id);
        return (file_exists ($fn)) ? file_get_contents ($fn) : false;
    }

    /**
     * See if a process is running given its pid (POSIX only)
     * @param int $pid
     * @return bool true if process with pid $pid is running
     */
    function bgpIsRunning ($pid = null) {
        $pid = ($pid == null) ? $this->bgpGetPid() : $pid;
        $n = sprintf ("/proc/%d", $pid);
        
        $r = false;
        if (file_exists ($n)) {
            $r = (posix_getsid ($pid) === false) ? false : true;
        }
        $this->_addToStatus("pid $pid is " . (($r == true) ? "" : "not ") . "running");
        return $r;
        
    }
    /**
     * Bring Out Your Dead
     * See if the process has died ungracefully and update plus return status if so
     * @return boolean has the process running this just exited without cleaning up?
     */
    function bgpBOYD () {
        $pid = $this->bgpGetPid();
        
        if ($this->field ('status') == 1 && $pid !== false)
            if ($this->bgpIsRunning ($pid) == false) {
                $this->_bgpProcessCrashed($this->data['Job']['id']);
                return true;
            }
        return false;
    }

    function _bgpProcessCrashed ($job_id) {
        $this->recursive = 0;
        if ($this->read ('Job.status', $job_id)) {
            $this->_addToStatus("Updating status of $job_id to 'crashed'");
            $d = array ('Job' => array ('id' => $job_id, 'status' => 3));
            $this->save ($d, array ('Job.status, Job.id'), false);
            return true;
        }
        return false;
    }

    function _bgpGetRunningJobs ($opts = null) {
        $defaults = array (
            'conditions' => array (
                'Job.status' => '1'
            ),
            'fields' => array (
                'Job.id'
            ),
        );
        $f = (is_array ($opts)) ? array_merge_recursive ($defaults, $opts) : $defaults;
        return $this->find('all', $f);
    }

    function bgpGlobalCorpseCollection () {
        $this->_addToStatus("GCC");
        $runningStatus = $this->_bgpGetRunningJobs();

        if (empty ($runningStatus)) return false;

        $numCorpses = 0;
        foreach ($runningStatus as $job) {
            if (!$this->bgpIsRunning($this->bgpGetPid($job['Job']['id']))) {
                // Job has crashed
                // @TODO (above true unless it finished normally in between DB query above and process running test immediately above - provision)
                $numCorpses++;
                $this->_bgpProcessCrashed($job['Job']['id']);
            }
        }
        $this->_addToStatus("Collected $numCorpses corpses.");
        return $numCorpses;
    }
    

	var $validate = array(
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'data' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'processor_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'status' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		/*'pub_ref' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),*/
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
    var $hasMany = array(
		'LabResult' => array(
			'className' => 'LabResult',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
    );
}
?>