
<?php
if (empty ($places['geonames'])) {
    ?>
    <div>
        <p class="error">
            Couldn't find any relevant places in Wikipedia. Try another spelling or a nearby place.
        </p>
    </div>
    <?php
}
else {
    ?>
    <div>
        <span class="help">Click the Map button to show the place on the map and the Use button to copy
        in the name and coordinates.</span>
    </div>
    <?php

    foreach ($places['geonames'] as $placeIndex => $placeInfo)
        echo $this->element ('wiz/placeSearchResultRow', array ('placeIndex' => $placeIndex, 'placeInfo' => $placeInfo));
    ?>

    <div>
        <span class="help">Data are kindly provided by <a href="http://www.geonames.org/">geonames.org</a>. Data are provided "as-is"; you should always verify them yourself.</span>
    </div>
    <?php
}

