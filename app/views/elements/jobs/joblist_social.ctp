<?php
/*$percentComplete = null;
if (isset ($job['Job']['data'])) {
    $jData = unserialize ($job['Job']['data']);
    if (isset ($jData['resume']) && is_array ($jData['resume'])) {
        $percentComplete = round (($jData['resume']['rowsParsed'] / $jData['resume']['nRows']) * 100);
    }
}*/

?><div style="float: left; display: inline-block;">Job <strong><?=$job['Job']['id'];?></strong></div><?php

if (isset ($job['Job']['percent_complete'])) {
    $job['Job']['percent_complete'] = round ($job['Job']['percent_complete'],1);
    ?>

        <div class="ui-progressbar ui-widget ui-widget-content ui-corner-all" style="display: inline-block; width: 66%; float: right; max-width: 100%;" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?=$job['Job']['percent_complete']?>">
            
            <div class="ui-progressbar-value ui-widget-header ui-corner-all" style="width: <?=$job['Job']['percent_complete'];?>%; max-width: 100%;">
                <?php
                $floatDir = ($job['Job']['percent_complete'] < 19) ? "left" : "right";
                ?>
                <div style="padding: 2px 5px; font-size: larger; display: inline-block; float: <?=$floatDir?>; font-weight: bold;" class="">
                    <?=$job['Job']['percent_complete'];?>%
                </div>
            </div>
        </div>
<div class="ui-helper-clearfix"></div>
    
    <?php
}


if (isset ($job['User']['name'])) $job['User']['name'] = str_replace (" ","&nbsp;",$job['User']['name']);
if (strlen ($job['User']['name']) > 0 && strlen ($job['User']['url']) > 0) 
    $nameStr = $this->Html->link ($job['User']['name'], $job['User']['url'], array (
        'target' => '_blank',
        'escape' => false
    ));
elseif (strlen ($job['User']['name'])> 0) 
    $nameStr = $job['User']['name'];
else
    $nameStr = "Anonymous";

if (strlen ($job['User']['name']) > 0) 
        echo "<br /><small><strong>{$nameStr}</strong></small>";
if (strlen ($job['User']['institution']) > 0) 
        echo "<small> · <em>{$job['User']['institution']}</em></small>";
if (0+$job['User']['id'] > 0) {
    // Job is assigned to a user (good!)
    if (strlen ($job['User']['photo']) > 0) 
        echo $this->Html->image($job['User']['photo'],array ('class' => 'profilePhoto')).'<div class="ui-helper-clearfix"></div>';

}
?>