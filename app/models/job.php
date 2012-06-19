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
    var $statusCodes = array ('pending', 'running', 'finished', 'error');

    var $maxThreads = 1; // maximum number of concurrent bg processors at a time

    private $jobDir = ''; // temporary folder for graph scratch, zipping etc.
    /**
     * To be run from CLI. Finds the next job in the queue and runs it.
     */
    function tryProcessNext () {
        echo "Trying to process next job...\n";
        if (!$this->_goodToGo()) {
            echo "Not good to go :-(\n";
            return false;
        }
        if (PHP_SAPI !== 'cli') {
            // DEBUG
            //die("nofork.");
            return $this->_forkToBackground ();
        }
        
        $next = $this->_getNext ();
        
        if (!$next) {
            echo "No more jobs!\n";
            return false; // if there's nothing to do
        }
        
        $this->read (null, $next['Job']['id']);
        

        $this->_startProcessing();
        $input = unserialize ($this->field('data'));
        // the parser loads user input in whatever-ass format into the ttkpl object model
        $parsed = $this->_task ($this->field ('parser_name'), 'parser',     $input);
        // the processor takes this bunch of objects and makes them work
        $output = $this->_task ($this->field ('processor_name'), 'processor',  $parsed);
        // assuming nothing went wrong with the above steps, the reporter makes nice graphs and pdfs
        $report = $this->_task ($this->field ('reporter_name'), 'reporter',   $output);
        $this->_stopProcessing();

        // once complete, start a new process (to process the next job, if any) and exit
// DEBUG: This will cause an infinite loop if this thread fails to change the status of the current job
// @todo implement checking whether max number of processor threads has been reached.
        //sleep (1);
        //$this->_forkToBackground();
        exit (0);

    }
    function _getNext () {
        return $this->find('first', array (
            'order' => 'Job.id ASC',
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
        if (method_exists($this, $meth))
            return $this->$meth ($args);
        $this->_addToStatus("Error: Unknown task.");
        return false;
    }

    function _task_thermal_age_processor ($args) {
        $args = (array) $args;
        if (isset ($args[0]) && $args[0] == 'get_parser') return "dna_screener"; elseif (isset ($args[0]) && $args[0] == 'get_reporter') return "dna_screener"; // <-- default parser/reporter
        $this->_addToStatus ("Processor: Thermal Age");
        
        $ta = new \ttkpl\thermalAge();
        $ta->setKinetics($args['kinetics']);
        foreach ($args['Temporothermals'] as $tt)
            $ta->addTemporothermal ($tt);

        //print_r ($this->cleanse ($args));

        $this->_addToStatus("Calculating thermal age. This can take a long time, please be patient...");

        $taYrs = $ta->getThermalAge();

        $this->_addToStatus("Thermal age: " . $taYrs->getValue());

        //$ta->_nukeDataMess();

        return array ('thermalAge' => $ta, 'thermalYears' => $taYrs, 'objects' => array ($ta));
    }
    function _task_dna_screener_parser ($args) {
        static $temps = null;
        $this->_addToStatus ("Parser: DNA Screener");
        $parsed = array ();
        $parsed['Temporothermals'] = array (); // pretty much everything ends up in here

        // reaction
        $r = $this->_getReaction($args['reaction']['Reaction']['reaction_id']);
        if ($r !== false) {
            $kinetics = new \ttkpl\kinetics(
                $r['Reaction']['ea_kj_per_mol'],
                $r['Reaction']['f_sec'],
                $r['Reaction']['name'] . " (Source: {$r['Citation']['name']} [{$r['Citation']['id']}])"
            );
        }
        else {
            $kinetics = new \ttkpl\kinetics(
                $args['reaction']['Reaction']['ea_kj_per_mol'],
                $args['reaction']['Reaction']['f_sec'],
                $args['reaction']['Reaction']['name']
            );
        }
        $parsed['kinetics'] = $kinetics;

        $this->_addToStatus("Kinetics: Done");

        // soils
        $bur = new \ttkpl\burial();
        $addbur = false;
        if ($args['burial']['Burial']['numLayers'] > 0) {
            //echo "There are {$args['burial']['Burial']['numLayers']} burial layers in this TT. Encoding...\n";
            foreach ($args['burial']['SoilTemporothermal'] as $layer) {
                $s = $this->_getSoil($layer['soil_id']);
                if ($s !== false && $layer['thickness_m'] > 0) {
                    $std = \ttkpl\scalarFactory::makeThermalDiffusivity ($s['Soil']['thermal_diffusivity_m2_day']);
                    $z = \ttkpl\scalarFactory::makeMetres ($layer['thickness_m']);
                    $slayer = new \ttkpl\thermalLayer($z, $std, '');
                    $bur->addThermalLayer($slayer);
                    $addbur = true;
                }
                else {
                    $this->_addToStatus("Ignoring invalid soil layer " . $layer['order']);
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
        $parsed['Temporothermals'][] = $tt;

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
                $this->_addToStatus("Deferring to Thermal Age processor for {$args['unParsed'][$running]['specimen']['code']}");
                //print_r ($this->cleanse ($runIt)); die();
                $unParsed[] = $args['unParsed'][$running];
                $parsed[] = $runIt;
                $results[] = $this->_task_thermal_age_processor($runIt);
            }
        }
        else {
            $this->_addToStatus("Nothing to do!");
        }


        $args['output_spreadsheet_filename'] = preg_replace ('/\/input(\W)/', '/output$1', $args['spreadsheet_csv']['Spreadsheet']['filename']);
        $this->_addToStatus(sprintf ("Output spreadsheet filename will be: %s", $args['output_spreadsheet_filename']));
        return array (
            'unParsed' => $unParsed,
            'parsed' => $parsed,
            'results' => $results,
            'output_csv_url' => DS.'spreadsheets'.DS.basename ($args['output_spreadsheet_filename']),
            'output_csv_name' => basename ($args['output_spreadsheet_filename']),
            'output_csv_filename' => $args['output_spreadsheet_filename'],
            'spreadsheet_csv' => $args['spreadsheet_csv']
        );
    }
    // @TODO: (Braindump:) storage temporothermal isn't being added for some reason (fix this somewhere completely different)
    function _task_thermal_age_csv_reporter ($args) {

        $fn = @isset ($args['spreadsheet_csv']['Spreadsheet']['filename']) ? $args['spreadsheet_csv']['Spreadsheet']['filename'] : false;
        if (file_exists($fn)) {
            $this->_addToStatus(basename($fn) . " exists. Trying to open it...");
            $cp = new \ttkpl\csvData($fn, TRUE);
            $cp->addColumn("10C Thermal Age");
            $cp->addColumn("Effective Temperature");
            $this->_addToStatus("Headers found: " . implode ("|", $cp->titles));

            // slug  (-and then detect headers (not all are required)-)
            $s2e = array ();
            foreach ($cp->titles as $title)
                $s2e[strtolower (Inflector::slug($title))] = $title;

            foreach ($args['results'] as $resInd => $res) {
                $tao = $res['thermalAge'];
                $cp->setColumn($s2e['10c_thermal_age'], round ($tao->getThermalAge()->getValue(), 4));
                $cp->setColumn($s2e['effective_temperature'], round ($tao->getTEff()->getValue() + ttkpl\scalarFactory::kelvinOffset, 6));
                if (!$cp->next()) break;
            }
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
        
        file_put_contents ($this->bgpGetJobFileName ('report'), serialize ($this->cleanse($args)));
        // !!==borken
        //return true;
    }
    function _task_thermal_age_csv_parser ($args) {
        $this->_addToStatus ("Parser: Thermal Age CSV");

        // cache a bunch of boring stuff
        $rK = array (); $rS = array ();

        // load csv file

        $fn = @isset ($args['spreadsheet_csv']['Spreadsheet']['filename']) ? $args['spreadsheet_csv']['Spreadsheet']['filename'] : false;
        if (file_exists($fn)) {
            $this->_addToStatus(basename($fn) . " exists. Trying to open it...");
            $cp = new \ttkpl\csvData($fn, TRUE);
            $this->_addToStatus("Headers found: " . implode ("|", $cp->titles));

            // slug  (-and then detect headers (not all are required)-)
            $s2e = array ();
            foreach ($cp->titles as $title)
                $s2e[strtolower (Inflector::slug($title))] = $title;

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
            $rowCount = 0; // heading
            //$cp->next();
            do {
                $rowCount++;
                $row = $cp->current();
                $sid = $row[$cp->getColumn($s2e['specimen_id'])];
                $me = array ();
                //print_r ($row);

                if (empty ($row)) {
                    // empty row, do nothing
                    $this->_addToStatus("Empty Row " . $rowCount);
                }
                elseif (empty ($sid)) {
                    $this->_addToStatus("No ID in row " . $rowCount . "");
                    $row[$cp->getColumn($s2e['specimen_id'])] = 'noid-' . $rowCount;
                }

                if (!empty ($row)) {
                    $me = array (
                        'burial' => array (
                            'Temporothermal' => array ()
                        ),
                        'specimen' => array (
                            'name' => @$row[$cp->getColumn($s2e['specimen_name'])],
                            'code' => @$row[$cp->getColumn($s2e['specimen_id'])],
                        ),
                        'reaction' => array (
                            'Reaction' => array (
                                'reaction_id' => $row[$cp->getColumn($s2e['kinetics_id'])],
                                'ea_kj_per_mol' => $row[$cp->getColumn($s2e['energy_of_activation_kj_mol'])],
                                'f_sec' => $row[$cp->getColumn($s2e['pre_exponential_factor_s'])],
                                'name' => $row[$cp->getColumn($s2e['kinetics_name'])]
                            )
                        )
                    );
                    



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
                        );
                        // process burial layers if any
                        for ($ssln = 1; $ssln <= $SLCnum; $ssln++) {
                            if (!isset ($me['burial']['SoilTemporothermal'])) $me['burial']['SoilTemporothermal'] = array ();
                            if (isset ($row[$cp->getColumn($s2e['thickness_m_' . $ssln])]) && $row[$cp->getColumn($s2e['thickness_m_' . $ssln])] > 0) {
                                // layer is probably set
                                $dbSoil = $this->Soil->findById ($row[$cp->getColumn($s2e['soil_id_' . $ssln])]);
                                // thermal diffusivity is set; along with length this is all we actually /need/
                                if (isset ($row[$cp->getColumn($s2e['thermal_diffusivity_m2_day_' . $ssln])]) && $row[$cp->getColumn($s2e['thermal_diffusivity_m2_day_' . $ssln])] > 0) {
                                    $layr = array (
                                        'soil_id' => $row[$cp->getColumn($s2e['soil_id_' . $ssln])],
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

                                
                                $me['burial']['SoilTemporothermal'][] = $layr;
                                $me['burial']['Burial']['numLayers']++;
                            }
                        }
                        $me['burial']['Temporothermal']['startdate_ybp'] = ttkpl\scalarFactory::ad2bp ($row[$cp->getColumn($s2e['year_excavated_ad'])]);
                        $me['burial']['Temporothermal']['stopdate_ybp'] =  ($row[$cp->getColumn($s2e['year_deposited_bp'])]);
                    }


                    if (1) {
                        // deposition date
                        $me['storage']['Temporothermal']['startdate_ybp'] = ttkpl\scalarFactory::ad2bp ($row[$cp->getColumn($s2e['year_analysed_ad'])]);
                        $me['storage']['Temporothermal']['stopdate_ybp'] = ttkpl\scalarFactory::ad2bp ($row[$cp->getColumn($s2e['year_excavated_ad'])]);
                        $me['storage']['Temporothermal']['temp_mean_c'] = $row[$cp->getColumn($s2e['mean_temp_deg_c'])];
                        $me['storage']['Temporothermal']['temp_pp_amp_c'] = $row[$cp->getColumn($s2e['temp_range_tmax_tmin_deg_k'])];
                    }

                    //print_r ($me);

                }
                if (!empty ($me)) {
                    $parse = $this->_task_dna_screener_parser ($me);
                    //print_r ($this->cleanse ($parse));
                    $unParsed[] = $me;
                    $parsed[] = $parse;
                    $this->_addToStatus ("Parsed row " . $rowCount);
                }
                else
                    $this->_addToStatus ("Nothing to do for this row " . $rowCount);
                //$this->_addToStatus("<pre>".serialize ($parse)."</pre>");
                

            } while ($cp->next());

            $rtn = array (
                'unParsed' => $unParsed,
                'parsed' => $parsed,
                'spreadsheet_csv' => $args['spreadsheet_csv']
            );
            //file_put_contents (APP . DS . "csvdbg.txt", serialize ($this->cleanse($parsed), true));
            return $rtn;
        }
        return false;


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
    function _task_dna_screener_reporter ($args) {
        $this->_addToStatus ("Reporter: DNA Screener");
        global $tempDir;
        $tempDir = $this->_makeJobTmpDir() . "/";

        //die ("OH NOES I HAS CRASHED!1\n");

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
                $ttStats[] = sprintf ("%d sample yrs at %3.2f spls/sec (~%3.1fs)", $numSpls, $tt->twData['spl_yrs_sec'], $numSpls * $tt->twData['spl_yrs_sec']);
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
            $plot->set ("style fill solid 0.25 noborder");
            $plot->plotData( $dLS, 'filledcu', '1:2:3', 'x1y1');
            $plot->plotData( $dLB, 'filledcu', '1:2:3', 'x1y1');
        }
        //$plot->plotData( $dM, 'lines', '1:2', 'x1y1', 'smooth bezier');
        $plot->plotData( $dM, 'lines', '1:2', 'x1y1');
        $plot->plotData( $da, 'lines', '1:2', 'x1y1');
        $plot->plotData( $dga, 'lines', '1:2', 'x1y1'); // */// <-- fix me!

        if (isset($opts['temporothermal']->twData['teff'])) {
            $plot->plotData( $dT, 'lines', '1:2', 'x1y1');
        }

        $plot->setRange('x', $opts['temporothermal']->startDate->getYearsBp(), $opts['temporothermal']->stopDate->getYearsBp());

        //$plot->set ("size ratio 0.5");
        
        //$plot->export('thermal_age_test.png');

        //$plot->close();

        // BADNESS ENDS.

        $n = $opts['web_uri_path'] . sprintf ("%s_%s.%s", $opts['file_id'], $opts['filename_base'], $opts['file_ext']);
        $fn = $opts['webroot'] . $n;
        
        $plot->export($fn);
        $this->_addToStatus("Saving temporothermal graph to $fn");
        $plot->close();

        return (file_exists ($fn)) ? $n : false;

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
                "Ötzi" => 0.009088,
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
        $n = $opts['web_uri_path'] . sprintf ("%s_%s.%s", $opts['file_id'], $opts['filename_base'], $opts['file_ext']);
        $fn = $opts['webroot'] . $n;
        $this->_addToStatus("Saving lambda graph to $fn");
        $plot->plot($fn);
        return (file_exists ($fn)) ? $n : false;

    }

    function _graphDefaults () {
        return array(
            'web_uri_path' => 'reports/', // with trailing/ not /leading slash <-- bad nerd poetry?
            'filename_base' => 'user_graph',
            'file_ext' => 'svg',
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

    function cleanse ($arrIn, $maxN = 1000, $maxL = 6, $l = 0) {
        foreach ($arrIn as $i => &$c) {
            if (is_object($c)) {
                $d = array ();
                foreach ($c as $x => $y)
                    $d[$x] = $y;
                $c = $d;
            }
            if (is_array ($c)) {
                ++$l;
                if (count ($c) > 1000 || $l > $maxL)
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
    function _startProcessing () {
        //die();
        $id = $this->field ('id');
        if ($id !== false) {

            App::import ('Vendor', 'ttkpl/lib/ttkpl');

            // Currently we only need to import models for stuff which will only have been input by
            // record ID during data capture.
            foreach (array ('Reaction', 'Soil') as $importModel)
                $this->$importModel = ClassRegistry::init ($importModel);

            foreach (array ('pid', 'status') as $f)
                $this->bg[$f] = $this->bgpGetJobFileName($f);
            file_put_contents($this->bg['pid'], posix_getpid ());
            file_put_contents($this->bg['status'], '');
            $this->_addToStatus("Starting processor for job $id");
            $this->bg['startTime'] = microtime (true);

            $this->save (array ('Job' => array ('id' => $id, 'status' => 1)), false);

            return true;
        }
        return false;
    }
    /**
     * Cleans up runtime files
     */
    function _stopProcessing ($error = false) {
        $id = $this->field ('id');
        if ($id !== false) {
            $this->bg['stopTime'] = microtime (true);
            $this->_addToStatus("Finished job $id");
            $this->_addToStatus("Total runtime was " . ($this->bg['stopTime'] - $this->bg['startTime']));
            unlink ($this->bg['pid']);
//DEBUG!!!
            $this->save (array ('Job' => array ('id' => $id, 'status' => ($error == FALSE) ? 2 : 3)), false);

            return true;
        }
        return false;
    }
    /**
     * Adds a timestamped message to the TOP of the status file
     * @return bool false if status file doesn't exist or can't be written else true
     */
    function _addToStatus ($message) {
        if (!empty ($this->bg) && !empty ($this->bg['status']) && file_exists ($this->bg['status'])) {
            $st = file_get_contents ($this->bg['status']);
            $fmsg = sprintf("%f %s\n", microtime (true), $message);
            echo $fmsg;
            $st = $fmsg . $st;
            return file_put_contents ($this->bg['status'], $st);
        }
        return false;
    }
    /**
     * Spawns a background process to run tryProcessNext
     */
    function _forkToBackground () {
        $command = "php -q " . trim (`pwd`) . "/../../cake/console/cake.php --app " . APP . " background";
        //die ("nohup $command > /dev/null 2>&1 & echo $!");
        $pid = shell_exec ("nohup $command > /dev/null 2>&1 & echo $!");
        return ($pid);
    }
    /**
     * Run by tryProcessNext prior to doing any work; initialises
     * @return bool is it ok to create another bg processing thread
     */
    function _goodToGo () {
        // if running threads < maxThreads return true
        return true;
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
                    'Job.status <=' => 1,
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
    function bgpGetPid () {
        $pid = $this->_bgpGetJobFileContent('pid');
        return ($pid == false) ? sprintf ("%d", $pid) : false;
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
    function bgpGetJobFileName ($file = 'pid') {
        $id = $this->field('id');
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
            $this->jobDir = $dir;
            return $dir;
        }
        return false;
    }
    function _clearJobTmpDir () {
        if (is_dir ($this->jobDir)) {
            //return rmdir($this->jobDir);
        }
    }
    /**
     * Generic function for getting stuff out of one of a jobs possible files
     * @param <type> $file see bgpGetJobFile
     * @return string file content or false if doesn't exist
     */
    function _bgpGetJobFileContent ($file = 'pid') {
        $fn = $this->bgpGetJobFileName($file);
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
        $this->_addToStatus("pid is " . (($r == true) ? "" : "not") . "running");
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
                $this->save (array ('Job' => array ('id' => $this->data['Job']['id'], 'status' => 3)), false);
                return true;
            }
        return false;
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
		'pub_ref' => array(
			'alphanumeric' => array(
				'rule' => array('alphanumeric'),
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
}
?>