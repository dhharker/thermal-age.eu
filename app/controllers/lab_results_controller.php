<?php
class LabResultsController extends AppController {

	var $name = 'LabResults';
    
    function index() {
		$this->LabResult->recursive = 0;
		$this->set('labResults', $this->paginate('LabResult',array (
            'LabResult.user_id' => $this->Auth->user('id')
        )));
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid labResults', true));
			$this->redirect(array('action' => 'index'), null, true, true);
		}
		$this->set('labResult', $this->LabResult->find('first',array (
            'conditions' => array (
                'LabResult.user_id' => $this->Auth->user('id'),
                'LabResult.id' => $id
            )
        )));
	}

	function add() {
		if (!empty($this->data)) {
            //print_r ($this->data);
            //die();
			$this->LabResult->create();
			$this->LabResult->set($this->data);
			if ($this->LabResult->validates() && $this->LabResult->save()) {
				$this->Session->setFlash(__('The lab results have been saved', true));
                $this->_redirectAfterDoingStuff($this->data['LabResult']['job_id']);
			} else {
				$this->Session->setFlash(__('The lab results could not be saved. Please, try again.', true));
			}
		}
        
		$this->_setStuffByJobId ($this->data['LabResult']['job_id']);
	}

	function edit($id = null, $job_id = null) {
        
        if ($job_id !== null) {
            $job_id = $job_id+0;
            //$this->set ('afterSuccess','job');
        }
        
        if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid lab results', true));
			$this->_redirectAfterDoingStuff($job_id);
		}
        elseif (!$id && !empty($this->data)) {
            $id = $this->data['LabResult']['id'];
            $job_id = $this->data['LabResult']['job_id'];
        }
        //print_r (compact ('id','job_id'));echo("AS");
		if (!empty($this->data) && $this->LabResult->set($this->data)) {
            //print_r (compact ('id','job_id'));die("AB");
			if ($this->LabResult->validates()) {
                $this->LabResult->_calculateLambdaFromExperimental ($this->data);
                $this->LabResult->save();
				$this->Session->setFlash(__('The lab results have been saved', true));
                //die ("jid is $job_id");
				$this->_redirectAfterDoingStuff($job_id);
			} else {
				$this->Session->setFlash(__('The lab results could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->LabResult->read(null, $id);
		}
        $this->set('editMode', true);
		$this->_setStuffByJobId ($this->LabResult->data['Job']['id']);
	}

	function delete($id = null, $job_id = null) {
		if (!$id || $this->authoriseWrite('LabResults',$id)) {
			$this->Session->setFlash(__('Invalid id for lab results or not authorised', true));
			$this->_redirectAfterDoingStuff($job_id);
		}
		if ($this->LabResult->delete($id)) {
			$this->Session->setFlash(__('Lab result deleted', true));
			$this->_redirectAfterDoingStuff($job_id);
		}
		$this->Session->setFlash(__('Lab result was not deleted', true));
		$this->_redirectAfterDoingStuff($job_id);
	}
    
    
    function job ($job_id = null) {
        if ($job_id === null && isset ($this->data['LabResult']) && isset ($this->data['LabResult']['job_id']))
            $job_id = $this->data['LabResult']['job_id'];
        if (!empty ($this->data)) {
            $this->data['LabResult']['job_id'] = $job_id;
            $this->data['LabResult']['user_id'] = $this->Auth->user('id');
        }
        $this->_setStuffByJobId ($job_id);
        if (!empty($this->data)) {
            //print_r ($this->data);die();
			$this->LabResult->create();
			$this->LabResult->set($this->data);
			if ($this->LabResult->validates() && $this->LabResult->save()) {
				$this->Session->setFlash(__('The lab results have been saved', true));
                $this->redirect(array('action' => 'job', $this->data['LabResult']['job_id']), null, true, true);
			} else {
				$this->Session->setFlash(__('The lab results could not be saved. Please, try again.'.print_r ($this->LabResult->validationErrors,1), true));
			}
        }
        $authd = $this->authoriseWrite('Job',$job_id);
        if ($authd !== true) {
            $this->set('showForm', false);
        }
            
    }
    
    function job_multi ($job_id = null) {
        if ($job_id === null && isset ($this->data['LabResult']) && isset ($this->data['LabResult']['job_id']))
            $job_id = $this->data['LabResult']['job_id'];
        if (!empty ($this->data)) {
            $this->data['LabResult']['job_id'] = $job_id;
            $this->data['LabResult']['user_id'] = $this->Auth->user('id');
        }
        $this->_setStuffByJobId ($job_id);
        if (!empty($this->data)) {
            //print_r ($this->data);die();
			$this->LabResult->create();
			$this->LabResult->set($this->data);
			if ($this->LabResult->validates() && $this->LabResult->save()) {
				$this->Session->setFlash(__('The lab results have been saved', true));
                $this->redirect(array('action' => 'job', $this->data['LabResult']['job_id']), null, true, true);
			} else {
				$this->Session->setFlash(__('The lab results could not be saved. Please, try again.'.print_r ($this->LabResult->validationErrors,1), true));
			}
        }
        $authd = $this->authoriseWrite('Job',$job_id);
        if ($authd !== true) {
            $this->set('showForm', false);
        }
        $this->set('job', $this->LabResult->Job->read (null, $job_id));
    }
    
    function csv_results_template ($job_id = null) {
        
        $this->autoLayout = false;
        $this->autoRender = false;
        
        $j = $this->LabResult->Job->read(null, $job_id);
        
        if ($j !== false) {
            $fn = $this->LabResult->Job->bgpGetJobFileName ('report');
            if (file_exists ($fn)) {
                $results = file_get_contents ($fn);
                $uns = @unserialize($results);
                if ($uns !== false)
                    $results = $uns;
            }
            else
                $this->cakeError ('error404');
        }
        else
            $this->cakeError ('error404');
        
        
        if (!isset ($results['output_csv_filename']) || !file_exists($results['output_csv_filename'])) {
            $this->cakeError ('error404');
        }
        
        $tplFilename = preg_replace ('@^(.*?)(\.csv)$@i', '\1-experimental_results_template\2', $results['output_csv_filename']);
        $tplFileUrl  = preg_replace ('@^(.*?)(\.csv)$@i', '\1-experimental_results_template\2', $results['output_csv_url']);
        if (!file_exists ($tplFilename)) {
            App::import ('Vendor', 'ttkpl/lib/ttkpl');
            $csv = new \ttkpl\csvData ($results['output_csv_filename']);
            $numSets = 1;
            $colNames = array (
                'PCR %d Target Length',
                'PCR %d Num Runs',
                'PCR %d Num Successes',
                'HTP %d Mean Fragment Length Less Contaminants',
            );
            for ($i = 1; $i <= $numSets; $i++)
                foreach ($colNames as $cn)
                    $csv->addColumn(sprintf ($cn, $i));
            $csv->export($tplFilename);
        }
        
        if (!file_exists ($tplFilename)) {
            $this->cakeErorr ('error404');
        }
        else {
            $this->redirect ($tplFileUrl);
        }
        
    }
    
    function regression ($job_id) {
        
        App::import ('Vendor', 'ttkpl/lib/ttkpl');
        
        if ($job_id === null && isset ($this->data['LabResult']) && isset ($this->data['LabResult']['job_id']))
            $job_id = $this->data['LabResult']['job_id'];
        
        $this->LabResult->Job->set (array ('Job' => array ('id' => $job_id)));
        if (!$this->LabResult->Job->exists($job_id))
            $this->cakeError ('error404');
        
        $x = $y = array ();
        
        $res = $this->LabResult->find('all', array (
            'conditions' => array (
                'LabResult.job_id' => $job_id,
                'OR' => array (
                    'LabResult.user_id' => $this->Auth->user('id'),
                    'LabResult.shared' => 1,
                    'LabResult.modelled_lambda >' => '0',
                    'LabResult.lambda >=' => '0',
                )
            ),
            'fields' => array (
                'LabResult.id',
                'LabResult.lambda',
                'LabResult.modelled_lambda'
            )
        ));
        foreach ($res as $r) {
            $x[] = $r['LabResult']['modelled_lambda'];
            $y[] = $r['LabResult']['lambda'];
        }
        
        $llr = new \ttkpl\linearRegression($x, $y);
        $r2 = $llr->regRSqPc();
        $a = $llr->bfA();
        $b = $llr->bfB();
        $stro = sprintf ("%d%% of the variation in measured λ can be explained by %f × [modelled λ] + %f", $r2, $a, $b);
        
        $graph = new \ttkpl\ttkplPlot("Modelled and Measured λ (Job:{$job_id})\\n$stro");
        $graph->labelAxes("Modelled λ", "Measured λ");
        $graph->setData("Job $job_id Experiments", 0, 'x1y1', 'points');
        foreach ($x as $i => $vx)
            $graph->addData($vx, $y[$i], 0);
        
        $graph->setData("Job $job_id Best Fit", 1);
        $minX = min($x); $maxX = max($x);
        $graph->addDataAssoc(array (
            $minX => ($a * $minX) + $b,
            $maxX => ($a * $maxX) + $b,
        ), 1);
        
        
        $url = DS . 'reports' . DS . $job_id . '_lab_results_regression_graph.svg';
        $filename = APP . WEBROOT_DIR . $url;
        if (file_exists ($filename))
            unlink ($filename) or die ("Permissions error removing existing regression graph for job $job_id");
        
        $graph->plot($filename);
        
        $this->autoLayout = false;
        $this->autoRender = false;
        ob_clean();
        
        if (file_exists ($filename)) {
            //$this->redirect ($url);
            echo file_get_contents ($filename);
        }
        else {
            $this->Session->setFlash ("Error: Couldn't make regression graph for some reason.");
            $this->_redirectAfterDoingStuff($job_id);
        }
        
    }
    
    function csv_results_upload ($job_id = null) {
        if (empty ($this->data['Spreadsheet']['file'])) {
            $this->Session->setFlash ('Missing file upload');
            $this->_redirectAfterDoingStuff($job_id);
        }
        elseif (empty ($this->data['Spreadsheet']['file']['tmp_name'])) {
            $etcur[] = "File failed to upload.";
        }
        else {
            // Attempt processing
            $etcur = array ();
            $etcur[] = "Importing Experimental Results from " . htmlspecialchars($this->data['Spreadsheet']['file']['name']);
            
            
            App::import ('Vendor', 'ttkpl/lib/ttkpl');
            $csv = new \ttkpl\csvData ($this->data['Spreadsheet']['file']['tmp_name']);
            
            if ($csv === false) {
                $etcur = "Error: Couldn't parse CSV";
            }
            else {
                // Identify available results columns (n >= 0 of PCR and HTP)
                $genericColGroups = array (
                    'pcr' => array (
                        'labs_ref' => 'PCR %d Experiment ID',
                        'pcr_tgt_length' => 'PCR %d Target Length',
                        'pcr_num_runs' => 'PCR %d Num Runs',
                        'pcr_num_successes' => 'PCR %d Num Successes'
                    ),
                    'htp' => array (
                        'labs_ref' => 'HTP %d Experiment ID',
                        'htp_mfl_less_contaminants' => 'HTP %d Mean Fragment Length Less Contaminants'
                    )
                );
                $realColGroups = array (
                    'pcr' => array (),
                    'htp' => array ()
                );
                
                foreach ($genericColGroups as $cgType => $cg) {
                    $n = 1;
                    $apc = true;
                    while ($apc === true) {
                        $append = array ();
                        foreach ($cg as $dbn => $gcn) {
                            $rcn = sprintf ($gcn, $n);
                            $rcind = $csv->getColumn($rcn);
                            if ($rcind === false && $dbn != 'labs_ref') {
                                $apc = false;
                            }
                            elseif ($rcind !== false)
                                $append[$dbn] = $rcind;
                        }
                        if ($apc !== false)
                            $realColGroups[$cgType][] = $append;
                        $n++;
                    }
                    
                }
                
                //print_r ($realColGroups); die();
                
                
                // Get index of specimen ID for use in generic experiment ID creation
                $sidInd = false;
                $λInd = false;
                foreach ($csv->titles as $index => $title) {
                    $slug = strtolower (Inflector::slug(str_replace (".","",$title)));
                    if ($slug == 'specimen_id')
                        $sidInd = $index;
                    elseif ($slug == 'λ')
                        $λInd = $index;
                }
                    
                // Iterate rows
                $dontStop = true;
                $rowCount = 0;
                do {
                    $row = $csv->current();
                    if (!$row) continue;
                    $rowCount++;
                    //DEBUG$etcur[] = "Start Row {$rowCount}";
                    // PCR cols
                    foreach ($realColGroups as $expType => $cgs) {
                        //DEBUG$etcur[] = strtoupper ($expType) . " Experiments:";
                        foreach ($cgs as $cgi => $cg) {
                            //DEBUG$etcur[] = "Group " . ($cgi+1);
                            $tryInsert = true;
                            $newData = array (
                                'user_id' => $this->Auth->user('id'),
                                'job_id' => $job_id,
                                'experiment_type' => $expType,
                                'result_type' => 'run',
                                'spreadsheet_row' => $rowCount,
                                'modelled_lambda' => (isset ($row[$λInd])) ? $row[$λInd] : -1
                            );
                            
                            // All fields per group except experiment id are required
                            foreach ($cg as $dbn => $colInd) {
                                
                                if (!empty ($row[$colInd]))
                                    $newData[$dbn] = $row[$colInd];
                                elseif ($dbn != 'labs_ref')
                                    $tryInsert = false;
                            }
                            $ids = "$rowCount:$expType:".($cgi+1);
                            if (empty ($newData['labs_ref']) && $sidInd !== false)
                                $newData['labs_ref'] = $row[$sidInd] . "-$ids";
                            elseif (empty ($newData['labs_ref']))
                                $newData['labs_ref'] = "($ids)";
                            
                            // Check for dupes
                            $existing = $this->LabResult->find ('first',array (
                                'conditions' => array (
                                    'LabResult.user_id' => $newData['user_id'],
                                    'LabResult.job_id' => $newData['job_id'],
                                    'LabResult.labs_ref' => $newData['labs_ref'],
                                    'LabResult.experiment_type' => $newData['experiment_type'],
                                    'LabResult.spreadsheet_row' => $newData['spreadsheet_row'],
                                ),
                                'fields' => array ('LabResult.id')
                            ));
                            $exid = (empty ($existing) || $existing === false) ? false : $existing['LabResult']['id'];
                            
                            if ($exid !== false && !!$tryInsert) {
                                if ($this->LabResult->delete($exid))
                                    $etcur[] = "Deleted existing Lab Result for " . $ids;
                                else
                                    $etcur[] = "Error deleting existing Lab Result for " . $ids;
                            }
                            if (!!$tryInsert) {
                                $this->LabResult->create();
                                $this->LabResult->set (array (
                                    'LabResult' =>  $newData
                                ));
                                if ($this->LabResult->validates()) {
                                    if ($this->LabResult->save())
                                        $etcur[] = "Saved Lab Result for " . $ids; 
                                    else
                                        $etcur[] = "Error saving Lab Result for " . $ids;
                                }
                                else {
                                    $etcur[] = "Validation Error in row {$rowCount} " . strtoupper ($expType) . "/" . ($cgi + 1) . ": " .
                                        print_r ($this->LabResult->validationErrors,1);
                                }
                            }
                        }
                    }
                } while ($csv->next() && !!$dontStop);
            }
        }
        $this->set (compact('job_id', 'etcur'));
    }
    
    function _redirectAfterDoingStuff ($job_id = null) {
        if (!$job_id) die ("no jid $job_id");
        $jid = ($job_id === null && isset ($this->data['LabResult']['job_id'])) ? $this->data['LabResult']['job_id'] : $job_id;
        if ((isset ($this->data['LabResult']['after_success']) && 
            $this->data['LabResult']['after_success'] == 'job' && 
            isset ($this->data['LabResult']['job_id']) && 
            $this->data['LabResult']['job_id'] > 0) ||
            $job_id !== null)
        {
            $this->redirect(array('action' => 'job', $jid), null, true, true);
        }
        else
            $this->redirect(array('action' => 'index'), null, true, true);
    }
    function _setStuffByJobId ($job_id = null) {
        if (!$this->LabResult->Job->idExists($job_id))
            $this->_redirectAfterDoingStuff($job_id);
            //$this->redirect (array ('action' => 'index'));
            //$this->cakeError('error404');
        
        //$j = $this->LabResult->Job->read(null, $job_id);
        $user_id = $this->Auth->user('id');
        $labResults = $this->LabResult->find('all', array (
            'conditions' => array (
                'LabResult.job_id' => $job_id,
                'OR' => array (
                    'LabResult.user_id' => $user_id,
                    'LabResult.shared' => '1'
                )
            )
        ));
        $this->set(compact('labResults','job_id'));
    }
    
}