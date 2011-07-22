<?php
class Specimen extends AppModel {
	var $name = 'Specimen';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
            'notEmpty' => array(
                'rule' => array('notEmpty'),
                'required' => true,
                'allowEmpty' => false,
            )
		),
	);

    function  beforeValidate($options = array()) {
        
        parent::beforeValidate($options);
        
    }
}
?>