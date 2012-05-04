<?php
/*
job name
spinner # soils
multiselect e.g. soil types
checkbox constant sine temp cols?
checkbox multi-period sample example?
*/

?>

<h1 class="sbHeading ui-corner-tl">
    Setup Spreadsheet
</h1>
<div class="grid_11 omega paddedCell_10_h">
    
    <p>
        Ok, we have created a blank spreadsheet for you to copy your data into. Click below to
        download it. Once you've got your data in press Continue to upload it again for processing.
    </p>
    <?php echo $this->Html->link(
        "Download Spreadsheet Template<br /><span class=\"subtler-text\"></span>",
        array ('controller' => 'wiz', 'action' => 'get_blank_spreadsheet'),
        array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
    <p>
        Don't worry about your session expiring while you edit the sheet, you can skip to the upload
        step from the first screen in this wizard without having to fill out all the forms again.
    </p>
</div>
<div style="clear: both"></div>
<?php echo $this->Form->create  ('Spreadsheet', array('id' => 'SpreadsheetForm', 'url' => $this->here, 'class' => 'ui-corner-all')); ?>    
    <?=$this->Form->input('Spreadsheet.passed_download_step', array(
        'type' => 'hidden',
        'value' => 1
    ));?>
<?

echo $this->element('wiz/wizardControlBar', array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>



