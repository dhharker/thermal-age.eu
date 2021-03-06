<?php
$status_colours = array (
    "#4cc8a1", // pending
    "#bb4cc8", // running
    "#5fb329", // finished
    "#c91501", // error
    "#71877D" // incomplete
);
$status_icons = array (
    '&#xe005;', // pending
    '&#xe026;', // running
    '&#xe033;', // finished
    '&#xe035;', // error
    '&#xe04f;' // incomplete
);
if (isset ($jobs) && is_array ($jobs) && count ($jobs) > 0) {
    echo '<ul class="objectList NUBs">';
    foreach ($jobs as $job) {
        
        $status_str = (isset ($JSCs[$job['Job']['status']])) ? $JSCs[$job['Job']['status']] : $job['Job']['status'];
        $status_colour = (isset ($status_colours[$job['Job']['status']])) ? $status_colours[$job['Job']['status']] : "#888888";
        $status_icon = (isset ($status_icons[$job['Job']['status']])) ? $status_icons[$job['Job']['status']] : "&#xe067;";
        
        $jd = unserialize($job['Job']['data']);
        $jr = (isset ($job['Job']['results_file']) && is_array ($job['Job']['results_file'])) ? 
            $job['Job']['results_file'] : 
            false; 
        
        $download = '';
        $download_uri = '';
        $jobTitle = '';
        $jobSubTitle = '';
        $note = '';
        $itemIcon = '';
        $title_uri = '';
        $modd_date = '';
        $areYouSure = false;
        
        $status_uri = array (
            'controller' => 'jobs',
            'action' => 'status',
            $job['Job']['id']
        );
        $report_uri = array (
            'controller' => 'jobs',
            'action' => 'report',
            $job['Job']['id']
        );
        $resume_uri = array (
            'controller' => 'wiz',
            'action' => 'resume_draft',
            $job['Job']['id']
        );
        if ($job['Job']['status'] >= 2) {
            $title_uri = $report_uri;
            if (isset($job['Job']['updated']))
                $taiw = $this->Time->timeAgoInWords($job['Job']['updated']);
                $taiw = explode(",", $taiw);
                $modd_date = $taiw[0];
                if (count ($taiw) > 1) $modd_date .= " ago";
                    
        }
        else {
            $title_uri = $status_uri;
        }
        
        if (!$jd) $note .= "No data found in job!<br />";
        if (!$jr) $note .= "Couldn't find results file.<br />";
        elseif ($job['Job']['status'] == 2 && isset ($jd['spreadsheet_csv']) && isset ($jd['spreadsheet_csv']['Spreadsheet']) && isset ($jd['spreadsheet_csv']['Spreadsheet']['filename']) && isset ($jr['output_csv_name'])) {
            // It is a completed spreadsheet job
            $itemIcon = $download = $this->Icons->i('&#xe04b;');
            $download = $this->Icons->i('&#xe056;')."CSV";
            $download_uri = $jr['output_csv_url'];
            $download = $this->Html->link ($download, $download_uri,array ('class' => 'fg-button ui-corner-all ui-state-default ui-priority-secondary', 'escape' => false));
            
        }
        elseif ($job['Job']['status'] == 2) {
            // Hopefully a completed standard run
            $itemIcon = $this->Icons->i('&#xe006;');

            
            if (isset ($jd['specimen']) && isset ($jd['specimen']['Specimen']) && isset ($jd['specimen']['Specimen']['name'])) {
                $jobTitle = $jd['specimen']['Specimen']['name'];
            }
        }
        elseif ($job['Job']['status'] == 0) {
            // Job is in queue
            $itemIcon = $this->Icons->i('&#xe06c;');
        }
        elseif ($job['Job']['status'] == 1) {
            // Job is currently running!
            $itemIcon = $this->Icons->i('&#xe042;');
        }
        elseif ($job['Job']['status'] == 3) {
            // Job is error :-(
            $itemIcon = $this->Icons->i('&#xe042;');
            $title_uri = $status_uri;
        }
        elseif ($job['Job']['status'] == 4) {
            // Job is incomplete (saved for later by user)
            $itemIcon = $this->Icons->i('&#xe009;');
            $title_uri = $resume_uri;
            $areYouSure = "Would you like to resume this wizard now?";
        }
        
        if (isset ($jd['specimen']) && isset ($jd['specimen']['Specimen']) && isset ($jd['specimen']['Specimen']['name'])) {
                $jobTitle = $jd['specimen']['Specimen']['name'];
            }
            elseif (isset ($job['Job']['title']) && strlen ($job['Job']['title']) > 0) {
                $jobTitle = $job['Job']['title'];
            }
            elseif (isset ($jd['spreadsheet_setup']['Spreadsheet']['name']))
                $jobTitle = $jd['spreadsheet_setup']['Spreadsheet']['name'];
            else {
                $jobTitle = sprintf('Untitled Spreadsheet');
            }
            if (@isset ($jd['spreadsheet_upload']['Spreadsheet']['file']['name']))
                $jobSubTitle = $jd['spreadsheet_upload']['Spreadsheet']['file']['name'];
            
            if (strlen($download) > 0)
                $download .="&ensp;";
        ?>
        <li>
            <div style="text-align: right; float: right; font-variant: small-caps; color: <?=$status_colour;?>"><?=$status_str;?>&ensp;<?=$this->Icons->i($status_icon)?><br />
            <?=$modd_date?></div>
            <strong><?=$this->Html->link ($itemIcon."&nbsp; ".$jobTitle, $title_uri, array ('escape' => false), $areYouSure);?></strong><br />
            <?=$download?>
            <?=$jobSubTitle?>
                
            <?php if (strlen ($note) > 0) echo "<p>$note</p>"; ?>
            
            <?php
            //var_dump ($jd);
            if (isset ($jd['site']['Site']['name']))
                echo $jd['site']['Site']['name'];
            //echo date("Y-m-d H:i:s",time());
            ?>
            <div class="ui-helper-clearfix"></div>
        </li>
        <?php
    }
    echo '</ul>';
}
?>