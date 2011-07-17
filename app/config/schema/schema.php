<?php 
/* SVN FILE: $Id$ */
/* App schema generated on: 2011-07-17 13:07:31 : 1310906131*/
class AppSchema extends CakeSchema {
	var $name = 'App';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $citations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'doi' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $reactions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL),
		'molecule_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 120),
		'reaction_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'ea_kj_per_mol' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'f_sec' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'citation_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $sites = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'lat_dec' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'lon_dec' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'citation_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $specimens = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $temporothermals = array(
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
	var $uploads = array(
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
	var $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 40),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 25),
		'email_priv' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL),
		'institution' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200),
		'bio' => array('type' => 'text', 'null' => true, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
}
?>