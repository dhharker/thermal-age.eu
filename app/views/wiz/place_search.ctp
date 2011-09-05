<?php
foreach ($places['geonames'] as $placeIndex => $placeInfo)
    echo $this->element ('wiz/placeSearchResultRow', array ('placeInfo' => $placeInfo));
