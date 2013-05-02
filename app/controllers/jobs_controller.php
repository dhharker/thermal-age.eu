<?php
class JobsController extends AppController {

	var $name = 'Jobs';
    var $components = array ('FormatJson');
    function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(array('system', 'published', 'report'));
    }
    
    
    // System status, ax fn
    function system ($mod = null) {
        
        // $mod controls how much info to show
        $mod = (in_array ($mod,array ('simple'))) ? $mod : null;
        $this->set(compact('mod'));
        
        // Get num processes which actually have PIDs (are really running)
        $this->Job->recursive = 0;
        $numProcs = array (
            'running' => $this->Job->_bgpCountRunningJobProcesses(),
            'maxThreads' => $this->Job->maxThreads,
            'queue' => 0
        );
        
        // Get currently running jobs
        $this->Job->recursive = 1;
        $running = $this->Job->_bgpGetRunningJobs(array (
            'fields' => array (
                'Job.id',
                'Job.user_id',
                'User.id',
                'User.name',
                'User.photo',
                'User.institution',
                'User.url'
            ),
            'limit' => $numProcs['maxThreads']
        ));
        $numProcs['statusRunning'] = count ($running);
        $queue = $this->Job->find('all',array (
            'fields' => array (
                'Job.id',
                'Job.user_id',
                'User.id',
                'User.name',
                'User.photo',
                'User.institution',
                'User.url'
            ),
            'conditions' => array (
                'Job.status' => '0'
            ),
            'limit' => 3
        ));
        //$numProcs['queue'] = count ($queue);
        $numProcs['queue'] = $this->Job->find('count',array (
            'conditions' => array (
                'Job.status' => '0'
            )
        ));
        
        $jobbage = array (&$running, &$queue);
        foreach ($jobbage as &$jobs) {
            if (is_array ($jobs))
                foreach ($jobs as &$job) {
                    $job['Job']['percent_complete'] = $this->Job->getJobPercentComplete($job['Job']['id']);
                }
        }
        
        // Handle header update info timestamps n stuff
        $data = compact ('running', 'numProcs', 'queue');
        $sd = serialize($data);
        if ($this->Session->read ('Job.status.lastStatusData') !== $sd) {
            // data have changed
            $this->Session->write ('Job.status.lastStatusData',$sd);
            $this->Session->write ('Job.status.lastStatusTime',time());
        }
        $this->set ($data);
        header ('ax-new-epoch: ' . time());
        header ('ax-latest-epoch: ' . $this->Session->read ('Job.status.lastStatusTime'));
    }
    
    // Ajax fn
    function job_list ($list = null, $since = 0) {
        if (isset ($_GET['since']) && is_numeric ($_GET['since']) && $_GET['since'] > 0)
            $since = $_GET['since'];
        $list = (in_array ($list, array ('recent','incomplete'))) ? $list : null;
        
        header ('ax-new-epoch: ' . time());
        
        $user_id = $this->Auth->user('id');
        $jobSections = $this->Job->getSectionsByUserId ($list, $user_id, $since);
        
        $latestTs = 0;
        if (!!$jobSections) foreach ($jobSections as $jobs) if (!!$jobs) foreach ($jobs as $job) {
            $jd = strtotime($job['Job']['updated']);
            if ($jd > $latestTs)
                $latestTs = $jd;
        }
        if ($latestTs > 0)
            header ('ax-latest-epoch: ' . $latestTs);
        else
            header ('ax-latest-epoch: -1');
        
        $JSCs = $this->Job->statusCodes;
        $this->set(compact('jobSections','JSCs'));
    }
    
    
    /**
     * Users get bounced here when a job is finished (with or without errors)
     * @param int $id of job to get report for
     */
    function report ($id = null) {
        if ($id === null)
            $id = $this->params['id'];
        $j = $this->Job->read(null, $id);
        
        //debug ($this->view); die();
        if ($j !== false) {
            $ar = $this->authoriseRead ('Job',$id);
            if ($ar !== true) {
                $this->Session->setFlash("Not authorised to view this job: " . $ar);
                $this->redirect ((!!$this->Auth->user('id')) ? array (
                    'controller' => 'users',
                    'action' => 'dashboard'
                ) : '/');
            }
                
            $status = $this->Job->bgpGetStatus ();
            $this->set ('status', $status);
            $fn = $this->Job->bgpGetJobFileName ('report');
            if (file_exists ($fn)) {
                $results = file_get_contents ($fn);
                $uns = @unserialize($results);
                if ($uns !== false)
                    $results = $uns;
            }
            else
                $results = "Couldn't find report, sorry!";
            $this->set ('results', $results);
            $this->set ('job', $j);
            $this->render ($j['Job']['reporter_name'] . '_report');
        }
        else {
            $this->Session->setFlash ("Couldn't find a Report for that Job.");
            $this->redirect(array (
                'controller' => 'users',
                'action' => 'dashboard'
            ));
        }

    }
    
    
    var $report_file_idents = array (
        'status' => array (
            'mime' => 'text/plain; charset=utf-8',
            'ext' => 'log'
        ),
        'report' => array (
            'mime' => 'text/plain; charset=utf-8',
            'unserialize2json' => true,
            'formatjson' => true,
            'ext' => 'json',
            'fnident' => 'summary'
        ),
    );
    /**
     * 
     * @param int $id of job
     * @param string $ident unique string identifying file/resource to serve
     */
    function report_files ($id, $ident) {
        $j = $this->Job->read(null, $id);
        if ($j === false || !isset ($this->report_file_idents[$ident]))
            $this->cakeError ('error404');
        if (!$this->authoriseRead ('Job') === true)
            $this->cakeError ('error404');
        
        $this->autoRender = false;
        $this->autoLayout = false;
        
        if (isset ($this->report_file_idents[$ident]['mime']))
            header ("Content-Type: {$this->report_file_idents[$ident]['mime']}\n");
        $strExt = (!empty($this->report_file_idents[$ident]['ext'])) ? '.'.$this->report_file_idents[$ident]['ext'] : '';
        $strIdent = (!empty($this->report_file_idents[$ident]['fnident'])) ? $this->report_file_idents[$ident]['fnident'] : $ident;
        header("Content-disposition: attachment; filename=thermal-age-eu_job-{$id}_{$strIdent}_".date('Y-m-d')."{$strExt}");
        
        $data = $this->_reportFilesGet($id, $ident);
        
        if (!empty ($this->report_file_idents[$ident]['unserialize2json']) && !!$this->report_file_idents[$ident]['unserialize2json'])
            $data = json_encode (unserialize ($data));
        
        if (!empty ($this->report_file_idents[$ident]['formatjson']) && !!$this->report_file_idents[$ident]['formatjson'])
            $data = $this->FormatJson->indent ($data);
        
        echo $data;
        
    }
    function _reportFilesGet ($id, $ident) {
        $rfn = $this->Job->bgpGetJobFileName($ident, $id);
        
        if (!!file_exists($rfn)) {
            $f = file_get_contents ($rfn);
            if ($f === false)
                $this->cakeError("error404");
            else
                return $f;
        }
        else {
            $this->cakeError("error404");
            return false;
        }
        
    }

    /**
     * Users are redirected here after submitting a job for processing.
     * @param int $id of job to get status of
     */
    function status ($id = null) {

        $this->disableCache();

        $j = $this->Job->read(array (
            'Job.id',
            'Job.title',
            'Job.status',
            'Job.created',
        ), $id);

        $skey = 'jobstatus' . $j['Job']['id'] . 'since';
        if ($this->Session->check ($skey)) {
            $since = $this->Session->read ($skey);
        }
        else {
            $since = time ();
        }
        $this->Session->write ($skey, time());

        $this->set ('job', $j);
        $status = $this->Job->bgpGetStatus ();
        $this->set ('status', $status);//$since)); ignoring this for now as status update page currently too simple for it
        $async = $this->RequestHandler->isAjax ();
        $this->set ('async', $async);
        
        
        if ($j['Job']['status'] >= 2) // if job is complete, with or without error
            $this->redirect(array('action' => 'report', $id));
        elseif ($j['Job']['status'] == 0 && !$async) // job is pending
            $this->Job->tryProcessNext();
        elseif ($j['Job']['status'] == 1) // job is running
            $this->Job->bgpBOYD(); // check if *this job* has crashed
        
        if (!!$async) {
            $sd = md5(serialize($status['statusFile']));
            if ($this->Session->read ('Job.statustxt.lastStatusData') !== $sd) {
                // data have changed
                $this->Session->write ('Job.statustxt.lastStatusData',$sd);
                $this->Session->write ('Job.statustxt.lastStatusTime',time());
            }
            header ('ax-new-epoch: ' . time());
            header ('ax-latest-epoch: ' . $this->Session->read ('Job.statustxt.lastStatusTime'));
        }
    }


	function index() {
		$this->Job->recursive = 0;
		$this->set('jobs', $this->paginate());
	}

	function view($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid job', true));
			$this->redirect(array('action' => 'index'));
		}
		$this->set('job', $this->Job->read(null, $id));
	}

	function add() {
		if (!empty($this->data)) {
			$this->Job->create();
			if ($this->Job->save($this->data)) {
				$this->Session->setFlash(__('The job has been saved', true));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The job could not be saved. Please, try again.', true));
			}
		}
        $this->_populateForm();
		$users = $this->Job->User->find('list');
		$this->set(compact('users'));
	}

	function edit($id = null) {
        
		if (!$id && empty($this->data)) {
			$this->Session->setFlash(__('Invalid job', true));
			$this->redirect(array('action' => 'index'));
		}
        if (!!$id && !$this->Job->idExists ($id) || !$this->authoriseWrite('Job',$id)) {
            $this->Session->setFlash(__('Invalid job', true));
			$this->redirect(array('controller' => 'users', 'action' => 'dashboard'));
        }
		if (!empty($this->data)) {
            if (isset ($this->data['Job']['publish_lab_results']) && !!$this->data['Job']['publish_lab_results'])
                $this->_publishAssociatedLabResults ($id, $this->data['Job']['published_date']);
            
            // J for Job (generic)
            $pubRefTypeIdentifier = 'J';
            $rptn = $this->Job->read('reporter_name', $id);
            switch ($rptn['Job']['reporter_name']) {
                // B for Batch, S for Single
                case "thermal_age_csv": $pubRefTypeIdentifier = 'B'; break; 
                case "dna_screener":    $pubRefTypeIdentifier = 'S'; break;
            }
            $this->data['Job']['pub_ref'] = 'TAEU-' . $pubRefTypeIdentifier . $id;
			if ($this->Job->save($this->data)) {
				$this->Session->setFlash(__('The job has been saved', true));
				$this->redirect(array('action' => 'report', $this->data['Job']['id']));
			} else {
				$this->Session->setFlash(__('The job could not be saved. Please, try again.', true));
			}
		}
		if (empty($this->data)) {
			$this->data = $this->Job->read(null, $id);
		}
        $this->_populateForm();
		$users = $this->Job->User->find('list');
		$this->set(compact('users'));
	}
    
    function publish_results ($job_id) {
        if ($this->authoriseWrite ('Job',$job_id) !== true) return false;
        $n = $this->_publishAssociatedLabResults($job_id);
        $msg = ($n === false) ? "Couldn't publish any results." : "Published " . ($n+0) . " lab results!";
        $url = ($this->authoriseRead ('Job',$job_id) === true) ? array (
            'action' => 'report',
            $job_id
        ) : array (
            'controller' => 'users',
            'action' => 'dashboard'
        );
        $this->Session->setFlash ($msg);
        $this->redirect ($url);
    }
    
    function _publishAssociatedLabResults ($job_id, $date = null) {
        if ($date === null) $date = (time () + (60 * 60 * 24));
        if ($this->authoriseWrite ('Job',$job_id) !== true) return false;
        $this->Job->LabResult->recursive = -1;
        $lrs = $this->Job->LabResult->find ('all', array (
            'conditions' => array (
                'LabResult.job_id' => $job_id,
                'LabResult.user_id' => $this->Auth->user('id'),
                //'LabResult.published NOT' => '1'
            ),
            'fields' => array (
                'id'
            )
        ));
        if (!!$lrs && !empty ($lrs))
            foreach ($lrs as $lr) {
                // E for Experimental result
                $lr['LabResult']['pub_ref'] = 'TAEU-E-' . $lr['LabResult']['id'];
                $lr['LabResult']['published'] = '1';
                $lr['LabResult']['published_date'] = date ('Y-m-d',strtotime($date));
                $this->Job->LabResult->set ($lr);   
                $this->Job->LabResult->save ();   
            }
        return count ($lrs);
    }
    
    function published ($pub_ref) {
        $job = $this->Job->find ('first', array (
            'conditions' => array (
                'Job.pub_ref LIKE' => $pub_ref,
                /*'OR' => array (
                    'Job.user_id' => $this->Auth->user('id'),
                    'AND' => array (
                        'Job.published' => '1',
                        'DATE(Job.published_date) >' => 'DATE('.date('Y-m-d').')'
                    )
                )*/
            )
        ));
        if (!$job)
            $this->cakeError ('error404');
        else {
            $ua = $this->authoriseRead ('Job', $job['Job']['id']);
            // Is embargoed & user not otherwise allowed to see?
            if (strtotime ($job['Job']['published_date']) > time() && $ua !== true) // yes
                $job['Job']['embargo'] = true;
            elseif ($ua != true) {
                $job['Job']['authorised'] = false;
                $job['Job']['authorised_message'] = $ua;
            }
            else
                $this->redirect (array (
                    'controller' => 'jobs',
                    'action' => 'report',
                    $job['Job']['id']
                ));
            $this->set(compact('job'));
        }
    }
    
    function _populateForm () {
        $names = array ('Parser', 'Processor', 'Reporter');
        $task_opts = array ();
        foreach ($names as $n) {
            $l = strtolower ($n);
            $t = $this->Job->tasks_available ($l);
            $task_opts[$l] = array_combine($t, $t);
        }
        return $this->set(compact('task_opts'));
    }
    
	function delete($id = null) {
		if (!$id) {
			$this->Session->setFlash(__('Invalid id for job', true));
			$this->redirect(array('action'=>'index'));
		}
		if ($this->Job->delete($id)) {
			$this->Session->setFlash(__('Job deleted', true));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Job was not deleted', true));
		$this->redirect(array('action' => 'index'));
	}
}
?>