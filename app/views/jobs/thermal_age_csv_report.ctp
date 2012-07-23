
<?php
//debug ($job);
if (isset ($job['Job']['id']) && is_numeric ($job['Job']['id'])) {
    $jid = $job['Job']['id'];
}
else {
    $jid = 0;
}

if ($jid == 0) {
    $this->error (404, 'Error', "Job not found.");
}


?>

<div class="grid_12 ">
    <div class="smartbox">
        <?php
        if (isset ($job['Job']['status']) && $job['Job']['status'] == 3) {
            $this->error (500, 'Error', "Unfortunately the job has crashed.");
        }
        elseif (!is_array ($results) || empty ($results['output_csv_url'])) {
            $this->error (500, 'Error', "Couldn't find results - job is probably one of not started, not finished or not successful.");
            debug ($results);
        }
        else {
            ?>

            <h1 class="sbHeading ui-corner-tl">
                Job Finished
            </h1>
            <p>
                Your job has finished. Please click below to download your spreadsheet now it has been
                populated with results.

            </p>
            <?php echo $this->Html->link(
                "Download Spreadsheet<br /><span class=\"subtler-text\">{$results['output_csv_name']}</span>",
                $results['output_csv_url'],
                array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
            <p>
                You can bookmark this page and come back to it but be aware it is not a permanent
                archive and you should keep a copy of any important results.
            </p>
        <?php
        }
        ?>
    </div>
</div>
<div style="clear: both"></div>