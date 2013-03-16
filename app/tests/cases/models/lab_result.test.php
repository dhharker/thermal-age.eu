<?php
/* LabResult Test cases generated on: 2013-03-12 11:51:48 : 1363089108*/
App::import('Model', 'LabResult');

class LabResultTestCase extends CakeTestCase {
	var $fixtures = array('app.lab_result', 'app.user', 'app.group', 'app.citation', 'app.reaction', 'app.site', 'app.upload', 'app.temporothermal', 'app.soil_temporothermal', 'app.soil', 'app.job');

	function startTest() {
		$this->LabResult =& ClassRegistry::init('LabResult');
	}

	function endTest() {
		unset($this->LabResult);
		ClassRegistry::flush();
	}

}
