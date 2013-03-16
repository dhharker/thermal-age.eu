<?php
/* LabResult Fixture generated on: 2013-03-12 11:51:48 : 1363089108 */
class LabResultFixture extends CakeTestFixture {
	var $name = 'LabResult';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'updated' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'htp_mfl_less_contaminants' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'pcr_tgt_length' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'pcr_num_runs' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'pcr_num_successes' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'job_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'notes' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'InnoDB')
	);

	var $records = array(
		array(
			'id' => 1,
			'user_id' => 1,
			'created' => '2013-03-12 11:51:48',
			'updated' => '2013-03-12 11:51:48',
			'htp_mfl_less_contaminants' => 1,
			'pcr_tgt_length' => 1,
			'pcr_num_runs' => 1,
			'pcr_num_successes' => 1,
			'job_id' => 1,
			'notes' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
		),
	);
}
