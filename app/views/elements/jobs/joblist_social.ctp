<?php
echo "Job <strong>{$job['Job']['id']}</strong>";
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
        echo " · <strong>{$nameStr}</strong>";
if (strlen ($job['User']['institution']) > 0) 
        echo "<br /><small><em>{$job['User']['institution']}</em></small>";
if (0+$job['User']['id'] > 0) {
    // Job is assigned to a user (good!)
    if (strlen ($job['User']['photo']) > 0) 
        echo $this->Html->image($job['User']['photo'],array ('class' => 'profilePhoto')).'<div class="ui-helper-clearfix"></div>';

}
?>