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
        if (!$this->_goodToGo())
            return false;
        if (PHP_SAPI !== 'cli')
            return $this->_forkToBackground ();
        
        $next = $this->_getNext ();
        
        if (!$next) {
            echo "Nothing to do!\n";
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


        $this->_addToStatus("Calculating thermal age. This can take a long time, please be patient...");

        $taYrs = $ta->getThermalAge();

        $this->_addToStatus("Thermal age: " . $taYrs->getValue());

        return array ('thermalAge' => $ta, 'thermalYears' => $taYrs);
    }
    function _task_dna_screener_parser ($args) {
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
                $args['reaction']['Reaction']['reaction_id'],
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
            $this->_addToStatus("Burial: Done");
        }
        else $this->_addToStatus("No burial layers specified");

        // storage temporothermal
        // @todo needs to support getting temperature data from uploaded CSV file stored in db
        $tt = new \ttkpl\temporothermal();
        $tt->setTimeRange(
            new \ttkpl\palaeoTime($args['storage']['Temporothermal']['startdate_ybp']),
            new \ttkpl\palaeoTime($args['storage']['Temporothermal']['stopdate_ybp'])
        );
        $storageSine = new \ttkpl\sine ();
        $storageSine->setGenericSine (
            \ttkpl\scalarFactory::makeCentigradeAbs ($args['storage']['Temporothermal']['temp_mean_c']),
            \ttkpl\scalarFactory::makeKelvinAnomaly ($args['storage']['Temporothermal']['temp_pp_amp_c']),
            \ttkpl\scalarFactory::makeDays (0));
        $storageSine->desc = $args['storage']['Temporothermal']['description'];
        $tt->setConstantClimate ($storageSine);
        $this->_addToStatus("Storage temperatures: Done");

        if ($addbur == true)
            $tt->setBurial ($bur);
        $parsed['Temporothermals'][] = $tt;
        

        // burial temporothermal (inc. site, soils)
        $tt = new \ttkpl\temporothermal();
        $temps = new \ttkpl\temperatures(); // temperature database (it is literally this easy lol)
        $tt->setTempSource($temps);
        $tt->setTimeRange(
            new \ttkpl\palaeoTime($args['burial']['Temporothermal']['startdate_ybp']),
            new \ttkpl\palaeoTime($args['specimen']['Temporothermal']['stopdate_ybp'])
        );
        $location = new \ttkpl\latLon (
            $args['site']['Site']['lat_dec'],
            $args['site']['Site']['lon_dec']
        );
        
        $location->desc = $args['site']['Site']['description'];
        $localisingCorrections = $temps->getPalaeoTemperatureCorrections ($location);
        $tt->setLocalisingCorrections ($localisingCorrections);
        $parsed['Temporothermals'][] = $tt;
        $this->_addToStatus("Burial conditions: Done");
        //print_r ($parsed);
        return $parsed;
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


        $ta = $args['thermalAge'];
        $taYrs = $args['thermalYears'];
        $results = array (
            'λ' => $ta->getLambda(),
            '(1/λ)+1' => 1 + (1 / $ta->getLambda()),
            'k (yr)' => $ta->getKYear (),
            'k (sec)' => $ta->getKSec (),
            'Teff' => \ttkpl\scalarFactory::makeCentigradeAbs ($ta->getTeff ())->getValue(),
            'Thermal age' => $taYrs->getValue(),
        );
        $report = $this->bgpGetJobFileName('report');
        $this->_addToStatus("Saving report to $report");
        file_put_contents($report, print_r ($results, true));

        


        $λ = $results['λ'];
        
        $plot = new \ttkpl\ttkplPlot("Fragment Length Distribution");
        $plot->labelAxes("DNA Fragment Length", "Relative Probability of survival through not-being-depurinated")
                ->setGrid(array ('x','y'))
                ->setLog(array ('x'))
                ->setData (sprintf ("λ = %0.6f", $λ), 1, 'x1y1', 'line');


        $mfl = round ((1/$λ)+1);
        $plot->setData ("Mean Fragment Length = $mfl", 2, 'x1y1', 'points')
             ->addData ($mfl, $this->Ps ($mfl, $λ), 2);

        for ($l = 0; $l <= $mfl * 10; $l += $this->_unprecision($l)) {
//print_r (array ($l, $this->Ps ($l, $λ), 2));
            $plot->addData ($l, $this->Ps ($l, $λ), 1);
        }

        $reportDir = trim(`pwd`) . '/webroot/reports/';
        
        $fn = $reportDir . "lambdas_fragment_lengths_" . $this->field ('id') . ".png";
        $this->_addToStatus("Saving lambda graph to $fn");
        $plot->plot($fn);



        $this->_clearJobTmpDir();
    }

    /**
     * Creates runtime files etc.
     * @return bool success
     */
    function _startProcessing () {
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
        if (file_exists ($this->bg['status'])) {
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
                    'Job.id <' => $this->field('id'),
                    'Job.status <' => 2,
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
            );

            if ($status == 1) { // Status = Running
                // Crash detection
                if ($this->bgpBOYD()) {
                    // ruh roh
                    $rtn['statusText'] = sprintf ("Uh oh, it looks like the job has crashed. Stand by for a status update!");
                }
                else {
                    $rtn['statusText'] = sprintf ("Your job is currently being processed.");
                    $rtn['statusFile'] = $this->bgpGetStatusFileSince ($since);

                }
                return $rtn;
            }
            else {
                return $this->bgGetProgress ();
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
        return file_exists (sprintf ("/proc/%d", $pid));
    }
    /**
     * Bring Out Your Dead
     * See if the process has died ungracefully and update plus return status if so
     * @return boolean has the process running this just exited without cleaning up?
     */
    function bgpBOYD () {
        if ($this->field ('status') == 1 && $this->bgpGetPid() !== false)
            if (!$this->bgpIsRunning ()) {
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