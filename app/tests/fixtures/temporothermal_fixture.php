<?php
/* Temporothermal Fixture generated on: 2011-07-16 19:07:11 : 1310840351 */
class TemporothermalFixture extends CakeTestFixture {
	var $name = 'Temporothermal';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'temp_mean_c' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'temp_pp_amp_c' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'upload_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'startdate_ybp' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'stopdate_ybp' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'range_years' => array('type' => 'float', 'null' => true, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'temp_mean_c' => 1,
			'temp_pp_amp_c' => 1,
			'upload_id' => 1,
			'startdate_ybp' => 1,
			'stopdate_ybp' => 1,
			'range_years' => 1,
			'user_id' => 1,
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.'
		),
	);
}
?>