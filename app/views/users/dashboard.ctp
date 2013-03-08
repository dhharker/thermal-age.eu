<div class="grid_7"><div class="smartbox">
<h1 class="sbHeading">Welcome, <?=$user['User']['alias']?></h1>

</div></div>
<div class="grid_5"><div class="smartbox">
<h2 class="sbHeading">Recent Jobs</h2>
<ul class="objectList">
<?php
$status_colours = array (
    "#4cc8a1", // pending
    "#bb4cc8", // running
    "#5fb329", // finished
    "#c91501", // error
);
if (isset ($jobs) && is_array ($jobs) && count ($jobs) > 0)
    foreach ($jobs as $job) {
        $note = "";
        $status_str = (isset ($JSCs[$job['Job']['status']])) ? $JSCs[$job['Job']['status']] : $job['Job']['status'];
        $status_colour = (isset ($status_colours[$job['Job']['status']])) ? $status_colours[$job['Job']['status']] : "#888888";
        //print_r ($job);die();
        $jd = unserialize($job['Job']['data']);
        $jr = (isset ($job['Job']['results_file']) && is_array ($job['Job']['results_file'])) ? 
            $job['Job']['results_file'] : 
            false; 
        $download = '';
        if (!$jd) $note .= "No data found in job!<br />";
        if (!$jr) $note .= "Couldn't find results file.<br />";
        elseif ($job['Job']['status'] == 2 && isset ($jd['spreadsheet_csv']) && isset ($jd['spreadsheet_csv']['Spreadsheet']) && isset ($jd['spreadsheet_csv']['Spreadsheet']['filename']) && isset ($jr['output_csv_name'])) {
            // It is a completed spreadsheet job
            $download = $this->Html->link ("Download","/".$jr['output_csv_url']);
                
        }
        ?>
        <li>
            <div style="float: right; font-variant: small-caps; color: <?=$status_colour;?>"><?=$status_str;?></div>
            <strong><?=$job['Job']['title'];?></strong><br />
            <span style="font-size: small;">Created: <?=$job['Job']['created'];?></span>
            <?php if (strlen ($note) > 0) echo "<p>$note</p>"; ?>
            
            <?php
            unset ($job['Job']['data']);
            echo @$download;
            ?>
        </li>
        <?php
    }
?>
</ul>
</div></div>