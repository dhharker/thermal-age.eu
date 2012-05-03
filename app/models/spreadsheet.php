<?php

class Spreadsheet extends AppModel {
    var $name = 'Spreadsheet';
    var $useTable = false;
    
    var $validate = array (
        'soil_cols_count' => array (
            'numeric' => array (
                'rule' => 'numeric',
                'message' => 'Must be a number!'
            ),
            'range' => array (
                'rule' => array ('numeric', 0, 5),
                'message' => 'Enter a value from 0 to 5 (inclusive).'
            )
        )
    );

    function __construct () {
        parent::__construct ();
    }
}