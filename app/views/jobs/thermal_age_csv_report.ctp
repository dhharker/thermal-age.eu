
<?php
//debug ($this->getVar('results'));
if (empty ($results['output_csv_url'])) {
    ?>
    <p>
    Error: Couldn't find output file :-(<br />Something must have gone wrong.
    </p>
    <?php
}
else {
    ?>

    <div class="grid_12 ">
        <div class="smartbox">
            <h1 class="sbHeading ui-corner-tl">
                Processing Complete!
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
        </div>
    </div>
    <div style="clear: both"></div>
    <?php
}
?>