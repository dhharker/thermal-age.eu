<?php
/*
 * Optional:
 *  $showForm   bool show form or not default true
 */
if (!isset ($showForm)) $showForm = true;

$fgbc = array ('class' => 'fg-button fg-button-secondary ui-state-default','style' => 'margin-top: -1px');
if (isset ($labResult) && is_array ($labResult)) {
    $l = &$labResult['LabResult'];
    
    
    if (in_array ($l['result_type'], array ('will_run','wont_run','hypothetical')))
        $iconStr = $l['result_type'];
    else if (strlen($l['experiment_type']) > 0) { // It is a real experiment not some null response
        if (in_array ($l['experiment_type'], array ('pcr','htp')))
            $iconStr = $l['experiment_type'];
    }
    echo $this->Html->image('lr_'.$iconStr.'_icon.png',array ('style' => 'float: left;'));
    echo '<div class="lrBigText">';
    
    if ($l['published'] == '1') {
        $shared = '<span style="color: #3366cc; display: inline-block; min-width: 5em;">'.$this->Icons->i('&#xe020;').' <strong>Published</strong></span>&ensp; ';
    }
    else {
        $shared = '<span style="color: #cc9933; display: inline-block; min-width: 5em;">'.$this->Icons->i('&#xe00d;').' <strong>Private</strong></span>&ensp; ';
    }
    $taiwCreated = $this->Time->timeAgoInWords($l['created']);
    $taiwUpdated = $this->Time->timeAgoInWords($l['updated']);
    $created = '<small style="font-style: italic">Added '.$taiwCreated.'</small>';
    if ($l['updated'] != $l['created'])
        $updated = '<small style="font-style: italic">Edited '.$taiwUpdated.'</small>';
    
    $colWs = array (3,3,3,3);
    $small = $big = array_fill(0,count($colWs),'');
    
    $small[2] = array ($shared, $created);
    if (isset ($updated) && $taiwUpdated != $taiwCreated)
        $small[2][] = $updated;
    
    $big[3] = '';
    $small[3] = array ();
    if (!!$showForm) {
        $actions = '<div style="" class="fg-buttonset fg-buttonset-single ui-helper-clearfix">';
        $delCor = 'right';
        if (0 && $l['result_type'] == 'run' && strlen ($l['experiment_type']) > 0 && in_array ($l['experiment_type'], array ('pcr','htp')))
            $actions .= $this->Html->link(__('Edit', true), array('action' => 'edit', $labResult['LabResult']['id'],$l['job_id']), array ('class' => $fgbc['class'] . " ui-corner-left", 'style' => $fgbc['style']));
        else
            $delCor = 'all';
        $actions .= $this->Html->link(__('Delete', true), array('action' => 'delete', $labResult['LabResult']['id'],$l['job_id']), array ('class' => $fgbc['class'] . " ui-corner-".$delCor, 'style' => $fgbc['style']), sprintf(__('Are you sure you want to delete # %s?', true), $labResult['LabResult']['id']));
        $actions .= '</div>';
        $small[3][] = $actions;
    }
    if (strlen($l['labs_ref']) > 0)
        $big[3] = '<span style="display: block; font-size: 20px; height: 35px; line-height: 38px; margin-top: -5px; overflow: hidden;">'.htmlspecialchars($l['labs_ref']).'</span>';
    
    $l['lambda'] = round ($l['lambda'], 4);
    
    switch ($iconStr) {
        case "pcr":
            $percent = round($l['pcr_percent']);//round (($l['pcr_num_successes'] / $l['pcr_num_runs']) * 100);
            
            $big[0] = '<small style="color: #8a8a8a">'.$this->Icons->i('&#xe06b;').'</small>&nbsp;'.$l['pcr_tgt_length'];
            //$big[1] = '<small style="color: #8a8a8a">'.$this->Icons->i('&#xe034;').'</small>&nbsp;<span style="color: #339933;">' . $l['pcr_num_successes'] . '</span>/' .  $l['pcr_num_runs'];
            $big[1] = '<span style="color: #8a8a8a; font-weight: bold; font-variant: normal;">&lambda;</span>'.'&nbsp;'.$l['lambda'];
            
            $small[0] = 'Amplicon Length';
            $small[1] = $percent.'% Success (<span style="color: #339933;">' . $l['pcr_num_successes'] . '</span>/' .  $l['pcr_num_runs'] . ')';
            
            break;
        case "htp":
            $percent = round($l['pcr_percent']);//round (($l['pcr_num_successes'] / $l['pcr_num_runs']) * 100);
            
            $big[0] = '<small style="color: #8a8a8a">'.$this->Icons->i('&#xe05e;').'</small>'.'&nbsp;'.$l['htp_mfl_less_contaminants'];
            $big[1] = '<span style="color: #8a8a8a; font-weight: bold; font-variant: normal;">&lambda;</span>'.'&nbsp;'.$l['lambda'];
            $big[2] = '';
            
            $small[0] = 'Average Length';
            $small[1] = "<strong>P</strong>({$l['htp_mfl_less_contaminants']}) = " . round (pow(1-$l['lambda'],$l['htp_mfl_less_contaminants']-1)*100,1) . "%";
            
            break;
        case "will_run":
            $big[0] = '<small style="color: #8a8a8a">'.$this->Icons->i('&#xe002;').'&ensp;Reminder'.'</small>';
            $small[0] = $l['remind_me'];
            $small[1] = '<a href="http://www.google.com/calendar/event?action=TEMPLATE&text=Upload%20experimental%20results%20for%20Job%20'.$l['job_id'].'&dates='.date('Ymd', strtotime($l['remind_me'])).'/'.date('Ymd', strtotime($l['remind_me']) + (60*60*24)).'&details=&location=http%3A%2F%2Fthermal-age.localhost%2Fjobs%2Freport%2F'.$l['job_id'].'&trp=false&sprop=http%3A%2F%2Fthermal-age.eu&sprop=name:thermal-age.eu" target="_blank"><img src="//www.google.com/calendar/images/ext/gc_button2.gif" border=0></a>';
            break;
        case "wont_run":
            $big[3] = '<small style="color: #8a8a8a">'.$this->Icons->i('&#xe061;')."&ensp;Won't Run".'</small>';
            break;
        case "hypothetical":
            $big[3] = '<small style="color: #8a8a8a">'.$this->Icons->i('&#xe061;')."&ensp;Couldn't Run".'</small>';
            break;
    }
    $numCells = 0;
    $rbInner = '';
    foreach ($colWs as $colI => $colW) {
        //echo '<div class="grid_'.$colW.' bigSmall">';
        $op = '<div class="grid_'.$colW.' bigSmall">';
        $showCell = false;
        $which = array ('big','small');
        foreach ($which as $class)
            if ((is_array (${$class}[$colI]) && !empty(${$class}[$colI])) || (!is_array (${$class}[$colI]) && strlen (${$class}[$colI]) > 0)) {
                $open = "<span class=\"$class\">";
                $op .= $open . implode ('</span>'.$open, (array)${$class}[$colI]).'</span>';
                $showCell = true;
                //echo $open . ${$class}[$colI] . '</span>';
            }
        $op .= '</div>';
        if (!!$showCell) {
            $rbInner .= $op;
            $numCells++;
        }
    }
    if ($numCells > 0) {
        echo '<div class="resultBarInner has-'.$numCells.'">';
        echo $rbInner;
        echo '</div>';
    }
    /*
    switch ($iconStr) {

        case "htp":
            echo '<span>';
                echo '<span style="display: inline-block; min-width: 4.5em;"><small style="color: #8a8a8a">'.$this->Icons->i('&#xe05e;').'</small>'.'&nbsp;'.$l['htp_mfl_less_contaminants'].'</span>&ensp; ';
                echo '<span><span style="color: #6ca689; font-weight: bold;">&lambda;</span>'.'&nbsp;'.$l['lambda'].'</span>&ensp;';
            echo '</span><span>';
            echo $timeAgo;
            echo '</span>';
            //$l['']
            
            break;
        case "will_run":
            echo '<span>';
                echo '<span><small style="color: #8a8a8a">'.$this->Icons->i('&#xe002;').'&ensp;Reminder on '.' '.$l['remind_me'].'</small></span>&ensp;';
                ?>
                <a href="http://www.google.com/calendar/event?action=TEMPLATE&text=Upload%20experimental%20results%20for%20Job%20<?=$l['id']?>&dates=<?=date('Ymd', strtotime($l['remind_me']))?>/<?=date('Ymd', strtotime($l['remind_me']) + (60*60*24))?>&details=&location=http%3A%2F%2Fthermal-age.localhost%2Fjobs%2Freport%2F<?=$l['id']?>&trp=false&sprop=http%3A%2F%2Fthermal-age.eu&sprop=name:thermal-age.eu" target="_blank"><img src="//www.google.com/calendar/images/ext/gc_button2.gif" border=0></a>
                <?php
            echo '</span><span>';
            echo $timeAgo;
            echo '</span>';
            //$l['']
            
            break;
        case "wont_run":
            //$l['']
            
            break;
        case "hypothetical":
            //$l['']
            
            break;
    }*/
    echo "</div>";
    //echo "id: ". $l['id'];
    ?>
    
    <div class="ui-helper-clearfix"></span>
    <?php
}
else {
    echo "Lab results: no data :-(";
}