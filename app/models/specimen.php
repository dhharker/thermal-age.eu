<?php
class Specimen extends AppModel {
	var $name = 'Specimen';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
            'between' => array(
                'rule' => array('between', 5, 15),
                'required' => true,
                'allowEmpty' => false,
                'message' => 'Between 5 to 15 characters'
            )
		),
        'code' => array(
            'rule' => array('notEmpty'),
            //'message' => 'wtf',
            //'allowEmpty' => false,
            'required' => true,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
		),
	);
}
?>