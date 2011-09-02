<?php 
/* SVN FILE: $Id$ */
/* App schema generated on: 2011-09-02 20:09:03 : 1314993543*/
class AppSchema extends CakeSchema {
	var $name = 'App';

	function before($event = array()) {
		return true;
	}

	function after($event = array()) {
	}

	var $citations = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 20, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g. paper, book, pers. comms., proceedings, ancient text, experimental data, dowsing results etc.', 'charset' => 'latin1'),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'the most "top level" url available which specificially relates to the resource', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'a fuller citation or additional information about the citation', 'charset' => 'latin1'),
		'doi' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'doi of the resource', 'charset' => 'latin1'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'owner of this record or null for global/public cites'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $feedback = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'uri' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'body' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'client_info' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'mood' => array('type' => 'float', 'null' => true, 'default' => '0'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $groups = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $reactions = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'Will generally be in the format molecule_name reaction_name (substrate_name)', 'charset' => 'latin1'),
		'molecule_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 120, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g. "DNA" (title case, should make sense read directly before reaction_name)', 'charset' => 'latin1'),
		'reaction_name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g. "Depurination" (title case, should make sense read directly after molecule_name)', 'charset' => 'latin1'),
		'substrate_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'comment' => 'Optional. e.g. "Bone" (title case, should make sense in brackets after name)', 'charset' => 'latin1'),
		'ea_kj_per_mol' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'f_sec' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'empty for public immutable records'),
		'citation_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'provenance optional :-)'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $sites = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'lat_dec' => array('type' => 'float', 'null' => false, 'default' => NULL, 'comment' => 'degrees north, decimal notation (-ve for south)'),
		'lon_dec' => array('type' => 'float', 'null' => false, 'default' => NULL, 'comment' => 'degrees east, decimal notation (-ve for west)'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'citation_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $soil_temporothermals = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'sudden' => array('type' => 'boolean', 'null' => false, 'default' => '0'),
		'temporothermal_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'soil_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'thickness_m' => array('type' => 'float', 'null' => false, 'default' => NULL),
		'order' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 3, 'comment' => '0 is surface'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $soils = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'thermal_diffusivity_m2_day' => array('type' => 'float', 'null' => false, 'default' => NULL, 'comment' => 'meters squared per second'),
		'particle_size' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 7, 'collate' => 'latin1_swedish_ci', 'comment' => 'clay, silt, sand, pebble', 'charset' => 'latin1'),
		'water_content' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 6, 'collate' => 'latin1_swedish_ci', 'comment' => 'dry, moist, saturated', 'charset' => 'latin1'),
		'citation_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $specimens = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'code' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $temporothermals = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g. "below sediment accumulation in north end of cave 2" or "stored in museum basement"', 'charset' => 'latin1'),
		'temp_mean_c' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => 'the mean daily air temperature at the ground surface (or the temperature of the sample, if unbuffered)'),
		'temp_pp_amp_c' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => 'the peak-peak amplitude of the annual variation in daily average temperature (i.e. max daily average temperature minus min blah blah blah temperature).'),
		'upload_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'the id of a csv file containing series of years_bp, temp_mean_c and optionally temp_pp_amp_c. if either is blank, value from db record is substituted.'),
		'startdate_ybp' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => 'the less recent date extent (inclusive) in years before 1950'),
		'stopdate_ybp' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => 'the more recent date extent (inclusive) in years before 1950'),
		'range_years' => array('type' => 'float', 'null' => true, 'default' => NULL, 'comment' => 'the number of years represented within this environment (e.g. for 1000 thermal years, a t_mean_c of 10, and all following fields to this blank and range_years of 1000)'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'user who owns this temporothermal. empty for public and immutable standard environments'),
		'description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'optional description of the environment (e.g. burial or museum storage), shouldn\'t duplicate information in associated burial layers or w/e', 'charset' => 'latin1'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $uploads = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'comment' => 'original filename', 'charset' => 'latin1'),
		'title' => array('type' => 'string', 'null' => false, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g. document title', 'charset' => 'latin1'),
		'size' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 12, 'comment' => 'file size in bytes'),
		'mime_type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 50, 'collate' => 'latin1_swedish_ci', 'comment' => 'mime type of content', 'charset' => 'latin1'),
		'description' => array('type' => 'text', 'null' => false, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'description of content e.g. data dictionary for a spreadsheet', 'charset' => 'latin1'),
		'citation_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10, 'comment' => 'owner of the file, null for '),
		'file_contents' => array('type' => 'binary', 'null' => true, 'default' => NULL, 'comment' => 'if null, look in filesystem.'),
		'file_location' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'comment' => 'e.g. "christmas/lunch-menu.pdf" - path relative to uploaded filestore root', 'charset' => 'latin1'),
		'indexes' => array(),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
	var $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'username' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 100, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'password' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 40, 'collate' => 'latin1_swedish_ci', 'comment' => 'sha256 hash', 'charset' => 'latin1'),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 25, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'email_priv' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'institution' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 200, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'bio' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'latin1_swedish_ci', 'charset' => 'latin1'),
		'created' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => false, 'default' => NULL),
		'group_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'latin1', 'collate' => 'latin1_swedish_ci', 'engine' => 'MyISAM')
	);
}
?>