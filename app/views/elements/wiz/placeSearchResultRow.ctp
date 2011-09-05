<?php

if (!empty ($placeInfo['summary'])) {
    ?>

    <div class="placeSearchResultRow">
        <?=@$placeInfo['']?>
        <div class="cell grid_1 alpha"><?=$placeInfo['title']?></div>
        <div class="cell grid_1">
            <span class="geo">
                <span class="latitude"><?=sprintf ("%-5.03f", $placeInfo['lat'])?></span>;
                <span class="longitude"><?=sprintf ("%-6.03f", $placeInfo['lng'])?></span>
            </span>
        </div>
        <div class="cell grid_8 omega"><?=$placeInfo['summary']?></div>
    </div>
    <?php
}
