<?php
class Geonames extends AppModel {
	var $name = 'Geonames';
	//var $displayField = 'name';
    public $useDbConfig = 'geoNames';
    public $useTable = false;

    /**
     * Returns some places for reverse-geocoding search on site form
     * @param string $place to search web services for.
     * @param int $maxRows to return
     */
    function placeSearch ($place, $maxRows = 5) {
        return $this->wikipediaSearch (array (
            'formatted' => false,
            'q' => urlencode($place),
            'style' => 'full',
            'maxRows' => $maxRows,
        ));
    }

}