<?php
/*
job name
spinner # soils
multiselect e.g. soil types
checkbox constant sine temp cols?
checkbox multi-period sample example?
*/
//debug ($this->viewVars);
?>

<h1 class="sbHeading ui-corner-tl">
    Setup Spreadsheet
</h1>
<div class="grid_11 omega paddedCell_10_h">
    <div class="spoiler">
        <p>
            Welcome to the Thermal Age Spreadsheet Tool. This wizard will help you create a blank
            spreadsheet to insert your values into with all the column headers in place and example
            rows to help you get started.
        </p>
        <p>
            If you already have a spreadsheet ready to go then
                <?=$this->Html->link ("click here to skip to the upload screen", array (
                    'controller' => 'wiz',
                    'action' => $this->action,

                ));?>
        </p>
        <p>
            Fields in <span style="font-weight: bold">bold<span style="color: #ee3322">*</span></span> are
            required.
        </p>
    </div>
</div>
<div style="clear: both"></div>
<?php echo $this->Form->create  ('Spreadsheet', array('id' => 'SpreadsheetForm', 'url' => $this->here, 'class' => 'ui-corner-all')); ?>
	<fieldset>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Spreadsheet.name', array(
                'label' => 'Job Name:',
                'placeholder' => 'e.g. "Northern Europe samples collection comparison"',
            ));?>
        </div>

        <div class="grid_4 alpha">
            <?=$this->Form->input('Spreadsheet.soil_cols_count', array (
                'label' => 'Max. # Soil Layers',
                'default' => 1,
                'placeholder' => '',
            ));?>
            <small>
                Enter the maximum number of soil layers you will need for any single period of burial.
            </small>
        </div>
        <div class="grid_5 omega clearfix">
            <?=$this->Form->input('Spreadsheet.example_soils', array(
                'type' => 'select',
                'multiple' => true,
                'options' => $this->getVar ('soils'),
                'label' => 'Include example soil info?',
                'placeholder' => 'Select any to include example rows for these soils',
                'class' => 'make-chosen',
                'style' => 'width: 100%;'
            ));?>
        </div>

        <div class="grid_11">
            <?= $this->Form->input('Spreadsheet.description', array('label' => 'Description:', 'rows' => 3));?>
        </div>
    </fieldset>

<?

echo $this->element('wiz/wizardControlBar', array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(document).ready (function () {
    wc.initSpreadsheetSetupForm ();
});
</script>


