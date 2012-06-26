<?php
class Upload extends AppModel {
	var $name = 'Upload';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'size' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'mime_type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	//The Associations below have been created with all possible keys, those that are not needed can be removed

	var $belongsTo = array(
		'Citation' => array(
			'className' => 'Citation',
			'foreignKey' => 'citation_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	var $hasMany = array(
		'Temporothermal' => array(
			'className' => 'Temporothermal',
			'foreignKey' => 'upload_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);


    /**
     * Downloads a resource from $url and saves it, returning the new local URL
     * @param string $url URL of the resource to cache
     */
    function passThrough ($url) {

        $filename = basename ($url);
        $urlHash = sha1 ($url);
        $dbFilename = $urlHash . $filename;

        $existing = $this->find('first', array (
            'conditions' => array (
                'Upload.name' => $dbFilename
            )
        ));
        if (empty ($existing)) {

            // Download the file
            set_error_handler(
                create_function(
                    '$severity, $message, $file, $line',
                    'throw new ErrorException($message, $severity, $severity, $file, $line);'
                )
            );
            try {
                $fileRaw = false;
                $fileRaw = file_get_contents ($url);
            }
            catch (Exception $e) {
                // @TODO smarten this up
                $nid = '';
            }
            restore_error_handler();

            $rawLen = strlen ($fileRaw);

            // did it work?
            if ($rawLen > 0) {
                // yes..?
                $this->save(array (
                    'Upload' => array (
                        'name' => $dbFilename,
                        'size' => $rawLen,
                        'file_contents' => $fileRaw,
                        'title' => $url,
                        'description' => 'Downloaded to local cache on ' . date ('Y-m-d H:i:s') . ' from ' . $url
                    )
                ));
                $nid = $this->getLastInsertID ();
            }
            else {
                // badness :-(
                $nid = '';
            }
            

        }
        else {
            $nid = $existing['Upload']['id'];
        }

        return sprintf ("/uploads/view/%01d",$nid);

    }


}
?>