<?php
/*
job name
spinner # soils
multiselect e.g. soil types
checkbox constant sine temp cols?
checkbox multi-period sample example?
*/

?>
<?=$this->element('wiz/wizardControlBarTop',  array ('wizardInfos' => $wizardInfos));?>
<h1 class="sbHeading">
    Setup Spreadsheet
</h1>

    <div class="grid_11 paddedCell_10_h">
        <p>
            Select the CSV file containing your data.
        </p>
    </div>

<div style="clear: both"></div>
<?php echo $this->Form->create  ('Spreadsheet', array('id' => 'SpreadsheetForm', 'url' => $this->here, 'class' => 'ui-corner-all noAjax', 'type' => 'file')); ?>
	<fieldset>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Spreadsheet.file', array(
                'type' => 'file',
                'label' => 'Upload CSV Data',
                
            ));?>
        </div>

    </fieldset>

<?

echo $this->element('wiz/wizardControlBar', array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>




