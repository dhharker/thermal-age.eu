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
            'soil_cols_count' => 1,
            'example_soils' => array (),
            'sine_cols' => 1,
            'multi_tt_examples' => 0,
            'custom_kinetics_cols' => 0,
            'Reaction' => array (array ('id' => 1))
        ), $arrOpts);
        
        $heads = array ();
        $numExampleRows = 2;
        $numExampleSoils = count ($opts['example_soils']);
        $xplRows = array_fill (0, $numExampleRows, array());
        $xplNotes = array ("", "");

        // If specified reaction is custom, force custom cols
        if (isset ($opts['Reaction']['reaction_id']) && $opts['Reaction']['reaction_id'] == -1)
            $opts['custom_kinetics_cols'] = 1;
        

        $heads[] = "Specimen ID";
            $xplRows[0][] = "EXAMPLE_1";
            $xplRows[1][] = "EXAMPLE_2";
        $heads[] = "Specimen Name";
            $xplRows[0][] = "Ötzi the Iceman";
            $xplRows[1][] = "York";
        $heads[] = "Year Analysed (AD)";
            $xplRows[0][] = "2012";
            $xplRows[1][] = "2010";
        
        
        if ($opts['sine_cols'] == 1) {
            $heads[] = "Year Excavated (AD)";
                $xplRows[0][] = "1991";
                $xplRows[1][] = "1980";
            $heads[] = "Mean Temp (deg. C)";
                $xplRows[0][] = "-6";
                $xplRows[1][] = "17";
            $heads[] = "Temp Range (tMax-tMin) (deg. K)";
                $xplRows[0][] = "0";
                $xplRows[1][] = "4";
        }

        $heads[] = "Year Deposited (b.p)";
            $xplRows[0][] = "5205";
                $xplNotes[0] .= "Ötzi died around 3255BC, which is 5205 years before 'present' (i.e. before 1950 AD). ";
            $xplRows[1][] = "1000";
        $heads[] = "Kinetics ID";
            $xplRows[0][] = "1";
                $xplNotes[0] .= "The Kinetics ID of 1 refers to DNA depurination in bone. If you fill in the ID, custom fields will be ignored. ";
        if ($opts['custom_kinetics_cols'] == 1) {
            $xplRows[1][] = "";
                $xplNotes[1] .= "To specify custom kinetics parameters, leave 'Kinetics ID' blank and include your parameters and reaction name on each required row. ";
            $heads[] = "Kinetics Name";
                $xplRows[0][] = "";
                $xplRows[1][] = $opts['Reaction']['name'];
            $heads[] = "Pre-exponential Factor (s)";
                $xplRows[0][] = "";
                $xplRows[1][] = $opts['Reaction']['f_sec'];
            $heads[] = "Energy of Activation (kJ/mol)";
                $xplRows[0][] = "";
                $xplRows[1][] = $opts['Reaction']['ea_kj_per_mol'];
        }
        else {
            $xplRows[1][] = "1"; // make e.g. #2 use DNA if custom cols not selected
        }
        $heads[] = "Site Name";
            $xplRows[0][] = "Ötztal Alps";
            $xplRows[1][] = "BioArCh";
        $heads[] = "Latitude (decimal)";
            $xplRows[0][] = "46.781732";
            $xplRows[1][] = "53.946776";
        $heads[] = "Longitude (decimal)";
            $xplRows[0][] = "10.840566";
            $xplRows[1][] = "-1.05800728";
        $heads[] = "Elevation (WGS84)";
            $xplRows[0][] = "3210";
            $xplRows[1][] = "17";

        $soilsOffset = count ($heads) - 1;
        $numSoilExRowsNeeded = ceil ($numExampleSoils / $opts['soil_cols_count']);
        $soilExRowBefore = $xplRows[1];
        if ($opts['soil_cols_count'] > 0) {
            for ($i = 1; $i <= $opts['soil_cols_count']; $i++) {
                $heads[] = "Soil ID #$i";
                $heads[] = "Soil Type #$i";
                $heads[] = "Thermal Diffusivity (m2/day) #$i";
                $heads[] = "Thickness (m) #$i";
                if ($i == 1) {
                    $xplRows[0][] = "2";
                    $xplRows[0][] = "Saturated Sand";
                        $xplNotes[0] .= "Ötzi was actually found embedded in ice but we use saturated sand in this example, only the ID (2) is needed. ";
                    if ($opts['soil_cols_count'] > 1)
                        $xplNotes[1] .= "When a sample is buried under multiple sediments, use more than one sets of soil fields. The first set is the surface layer, the last overlays the sample. ";
                    $xplRows[0][] = "";
                    $xplRows[0][] = "";
                }
                else {
                    $xplRows[0][] = "";
                    $xplRows[0][] = "";
                    $xplRows[0][] = "";
                    $xplRows[0][] = "";
                }
                if (!empty ($opts['example_soils'])) {
                    $es = array_shift ($opts['example_soils']);
                    $xplRows[1][] = $es['Soil']['id'];
                    $xplRows[1][] = $es['Soil']['name'];
                    $xplRows[1][] = $es['Soil']['thermal_diffusivity_m2_day'];
                    $xplRows[1][] = 1.2345;
                }
                else {
                    $xplRows[1][] = "";
                    $xplRows[1][] = "";
                    $xplRows[1][] = "";
                    $xplRows[1][] = "";
                }
            }
            //debug ($opts); die();
            $sci = 100;
            $xsi = 2;
            while (!empty ($opts['example_soils'])) {
                //echo "\nSTART:\t$sci:$xsi:{$opts['soil_cols_count']}:".count ($opts['example_soils']);
                if ($sci > $opts['soil_cols_count']) {
                    $sci = 1;

                    $xsi++;
                    $xplRows[$xsi] = $soilExRowBefore;
                    $xplRows[$xsi][0] = $xplRows[$xsi][0] . ".$xsi";
                }

                $es = array_shift($opts['example_soils']);
                $xplRows[$xsi][] = $es['Soil']['id'];
                $xplRows[$xsi][] = $es['Soil']['name'];
                $xplRows[$xsi][] = $es['Soil']['thermal_diffusivity_m2_day'];
                $xplRows[$xsi][] = 1.2345;
                
                //echo "\nEND:\t$sci:$xsi:{$opts['soil_cols_count']}:".count ($opts['example_soils']);
                $sci++;

            }
            if ($sci < ($opts['soil_cols_count'] + 1)) {
                $blanks = (($opts['soil_cols_count'] + 1) - $sci) * 4;
                for ($i = 0; $i < $blanks; $i++) {
                    $xplRows[$xsi][] = "";
                }
            }
        }
        //die();

        $heads[] = "Notes";
        foreach ($xplRows as $ri => $row)
            $xplRows[$ri][] = (isset ($xplNotes[$ri])) ? $xplNotes[$ri] : "(Extra row to show soil info)";
        
        $heads = array ($heads);
        $data = array_merge($heads, $xplRows);
        /*
        $fields = array (); $index == array ();
        foreach ($heads as $hi => $hv) {
            $fields[$hi] = strtolower (Inflector::slug ($hv));
            $index[$fields[$hi]] = $hi;
        }
        */
        //debug ($xplRows); die();
        $buffer = fopen('php://temp', 'w+');
        foreach ($data as $d)
            fputcsv($buffer, $d);
        rewind($buffer);
        $csv = fread ($buffer, 2048);
        
        fclose($buffer);

        return $csv;
        
    }



}