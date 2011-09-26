<?php
/* Upload Fixture generated on: 2011-07-16 19:07:26 : 1310839466 */
class UploadFixture extends CakeTestFixture {
	var $name = 'Upload';

	var $fields = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'mime_type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'citation_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'file_contents' => array('type' => 'binary', 'null' => true, 'default' => NULL),
		'file_location' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);

	var $records = array(
		array(
			'id' => 1,
			'name' => 'Lorem ipsum dolor sit amet',
			'title' => 'Lorem ipsum dolor sit amet',
			'mime_type' => 'Lorem ipsum dolor sit amet',
			'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
			'citation_id' => 1,
			'user_id' => 1,
			'file_contents' => 'Lorem ipsum dolor sit amet',
			'file_location' => 'Lorem ipsum dolor sit amet'
		),
	);
}
?>