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


    /* Build a blank spreadsheet with the right columns and example data in it to help users get
     * started quickly. */
    function get_blank_spreadsheet ($arrOpts = array ()) {
        $opts = array_merge (array (
            'name' => time(),
            'soils_col_count' => 1,
            'example_soils' => array (),
            'sine_cols' => 1,
            'multi_tt_examples' => 0,
            'custom_kinetics_cols' => 0,
            'Kinetics' => array (array ('id' => 1))
        ), $arrOpts);
        
        $heads = "Specimen ID	Specimen Name	Stop (more recent) Date (AD)	Stop (less recent) Date (b.p.)	Kinetics Name	Kinetics ID	Pre-exponential Factor (s)	Energy of Activation (kJ/mol)	Mean Temp (deg. C)	Temp Range (tMax-tMin) (deg. K)	Site Name	Latitude (decimal)	Longitude (decimal)	Soil Type 1	Thermal Diffusivity (m/s) 1	Thickness (m) 1";
        $heads = explode ("\t", str_replace (".", "", $heads));
        $fields = array ();
        foreach ($heads as $hi => $hv) {
            $fields[$hi] = strtolower (Inflector::slug ($hv));
        }

        return '"' . implode ('",'."".'"', $heads) . "\"\n";
        
    }



}