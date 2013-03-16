<?php
/* LabResults Test cases generated on: 2013-03-12 11:52:36 : 1363089156*/
App::import('Controller', 'LabResults');

class TestLabResultsController extends LabResultsController {
	var $autoRender = false;

	function redirect($url, $status = null, $exit = true) {
		$this->redirectUrl = $url;
	}
}

class LabResultsControllerTestCase extends CakeTestCase {
	var $fixtures = array('app.lab_result', 'app.user', 'app.group', 'app.citation', 'app.reaction', 'app.site', 'app.upload', 'app.temporothermal', 'app.soil_temporothermal', 'app.soil', 'app.job');

	function startTest() {
		$this->LabResults =& new TestLabResultsController();
		$this->LabResults->constructClasses();
	}

	function endTest() {
		unset($this->LabResults);
		ClassRegistry::flush();
	}

}
