<?php

if (!empty ($placeInfo['summary'])) {
    ?>
    <div class="smartsharp placeSearchResultRow">
        <?=@$placeInfo['']?>
        <?php
        if (strlen (@$placeInfo['thumbnailImg']) > 0) {
            ?>
        <img src="<?=$placeInfo['thumbnailImg']?>" style="margin: 5px; float: right;">
            <?php
        }
        ?>
        <div class="cell grid_2 alpha">
            <span style="font-weight: bold"><?=$placeInfo['title']?></span>
            (<?=@$placeInfo['countryCode']?>)
            <div>
                <?php echo $this->Html->link(
                    "Map",
                    '',
                    array(
                        'class' => 'fg-button ui-state-default ui-corner-all ui-priority-secondary rgsMapButton',
                        'escape' => false,
                        'style' => 'margin: 1px;')); ?>
                <?php echo $this->Html->link(
                    "Use",
                    '',
                    array(
                        'class' => 'fg-button ui-state-default ui-corner-all ui-priority-secondary rgsUseButton',
                        'escape' => false,
                        'style' => 'margin: 1px;')); ?>
            </div>
        </div>
        <div class="cell grid_2">
            <span class="geo">
                <span class="latitude"><?=sprintf ("%-5.03f", $placeInfo['lat'])?></span>;
                <span class="longitude"><?=sprintf ("%-6.03f", $placeInfo['lng'])?></span>
            </span>
            <br />
            <span class="elevation"><?=sprintf ("%-3.00f", @$placeInfo['elevation'])?></span>
        </div>
        <div class="summary cell omega"><?=$placeInfo['summary']?></div>
        <div style="clear: both"></div>
    </div>
    <?php
}
