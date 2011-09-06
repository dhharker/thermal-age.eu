
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
        <p class="help">
            Click the Map button to show the place on the map and the Use button to copy
            in the name and coordinates.
            <?php echo $this->Html->link(
            "Clear Results",
            '',
            array(
                'class' => 'fg-button ui-state-default ui-corner-all ui-priority-secondary rgsClearResultsButton',
                'id' => '',
                'escape' => false,
                'style' => 'margin-bottom: 2px; float: right; font-size: 10px;',
            )); ?>
        </p>
        
    </div>
    <?php

    foreach ($places['geonames'] as $placeIndex => $placeInfo)
        echo $this->element ('wiz/placeSearchResultRow', array ('placeIndex' => $placeIndex, 'placeInfo' => $placeInfo));
    ?>

    <div>
        <p class="help">
            Data are kindly provided by <a href="http://www.geonames.org/">geonames.org</a> under a <a href="http://creativecommons.org/licenses/by/3.0/" title="geonames.org data provided under a creative commons attribution license">CC by v3.0</a> license. Data are provided "as-is"; you should always verify them yourself.
        </p>
    </div>
    <?php
}

