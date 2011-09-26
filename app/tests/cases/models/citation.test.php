<?php
/* Citation Test cases generated on: 2011-07-15 16:07:52 : 1310742772*/
App::import('Model', 'Citation');

class CitationTestCase extends CakeTestCase {
	var $fixtures = array('app.citation', 'app.user', 'app.reaction', 'app.site');

	function startTest() {
		$this->Citation =& ClassRegistry::init('Citation');
	}

	function endTest() {
		unset($this->Citation);
		ClassRegistry::flush();
	}

}
?>