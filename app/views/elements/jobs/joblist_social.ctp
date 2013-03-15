<?php
/*$percentComplete = null;
if (isset ($job['Job']['data'])) {
    $jData = unserialize ($job['Job']['data']);
    if (isset ($jData['resume']) && is_array ($jData['resume'])) {
        $percentComplete = round (($jData['resume']['rowsParsed'] / $jData['resume']['nRows']) * 100);
    }
}*/
echo "Job <strong>{$job['Job']['id']}</strong>";

if (isset ($job['Job']['percent_complete'])) {
    $job['Job']['percent_complete'] = round ($job['Job']['percent_complete']);
    ?>
<div style="padding-right: 5px; float: right; text-align: right; font-weight: bold;" class="">
                    <?=$job['Job']['percent_complete'];?>%
                </div>
        <div class="ui-progressbar ui-widget ui-widget-content ui-corner-all" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="<?=$job['Job']['percent_complete']?>">
            <div class="ui-progressbar-value ui-widget-header ui-corner-left" style="width: <?=$job['Job']['percent_complete'];?>%;"></div>
        </div>
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