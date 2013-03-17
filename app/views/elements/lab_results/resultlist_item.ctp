<?php
$fgbc = array ('class' => 'fg-button ui-state-default','style' => 'margin-top: -1px');
if (isset ($labResult) && is_array ($labResult)) {
    $l = &$labResult['LabResult'];
    ?>
    <div style="float: right;" class="fg-buttonset fg-buttonset-single">
        <?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $labResult['LabResult']['id']), array ('class' => $fgbc['class'] . " ui-corner-bl", 'style' => $fgbc['style'])); ?>
        <?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $labResult['LabResult']['id'],$l['job_id']), $fgbc, sprintf(__('Are you sure you want to delete # %s?', true), $labResult['LabResult']['id'])); ?>
    </div>
    <?php
    
    if (in_array ($l['result_type'], array ('will_run','wont_run','hypothetical')))
        $iconStr = $l['result_type'];
    else if (strlen($l['experiment_type']) > 0) { // It is a real experiment not some null response
        if (in_array ($l['experiment_type'], array ('pcr','htp')))
            $iconStr = $l['experiment_type'];
    }
    echo $this->Html->image('lr_'.$iconStr.'_icon.png',array ('style' => 'float: left;'));
    echo '<div class="lrBigText">';
    switch ($iconStr) {
        case "pcr":
            $percent = round($l['pcr_percent']);//round (($l['pcr_num_successes'] / $l['pcr_num_runs']) * 100);
            echo '<span>';
                echo '<span><small style="color: #8a8a8a">'.$this->Icons->i('&#xe06b;').'</small>'.' '.$l['pcr_tgt_length'].'</span>&ensp;';
                echo '<span><span style="color: #6ca689; font-weight: bold;">&lambda;</span>'.' '.$l['lambda'].'</span>&ensp;';
            echo '</span><span>';
                echo '<span style="color: #339933"> ' .
                        '<small>'.$this->Icons->i('&#xe034;').'</small>' .
                        $l['pcr_num_successes'] .
                     '</span>/' . 
                        $l['pcr_num_runs'] . 
                        '<small>&Sigma;</small> &cong; ' .
                        $percent . "%";
            echo '</span>';
            //$l['']
            break;
        case "htp":
            echo '<span>';
                echo '<span><small style="color: #8a8a8a">'.$this->Icons->i('&#xe05e;').'</small>'.' '.$l['htp_mfl_less_contaminants'].'</span>&ensp;';
                echo '<span><span style="color: #6ca689; font-weight: bold;">&lambda;</span>'.' '.$l['lambda'].'</span>&ensp;';
            echo '</span><span>';
            echo 'How a-boot a big loong rambling ';
            echo '</span>';
            //$l['']
            
            break;
        case "will_run":
            //$l['']
            
            break;
        case "wont_run":
            //$l['']
            
            break;
        case "hypothetical":
            //$l['']
            
            break;
    }
    echo "</div>";
    //echo "id: ". $l['id'];
    ?>
    
    <div class="ui-helper-clearfix"></span>
    <?php
}
else {
    echo "Lab results: no data :-(";
}