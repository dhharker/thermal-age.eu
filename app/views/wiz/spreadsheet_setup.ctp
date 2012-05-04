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

        <div class="grid_5 alpha">
            <?=$this->Form->input('Spreadsheet.soil_cols_count', array (
                'label' => 'Max. # Soil Layers',
                'default' => 1,
                'placeholder' => '',
            ));?>
            <small>
                Enter the maximum number of soil layers you will need for any single period of burial (0-5).
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
        <div class="clear"></div>
        <div class="grid_5 alpha">
            <?=$this->Form->input('Spreadsheet.sine_cols', array (
                'type' => 'checkbox',
                'label' => 'Include constant (storage) sine columns?',
            ));?>
            <small>
                Include columns to model some time at a constantly varying temperature (described by
                the mean and amplitude of a sine wave) e.g. museum storage.
            </small>
        </div>
        <div class="grid_5 omega">
            <?=$this->Form->input('Spreadsheet.multi_tt_example', array (
                'type' => 'checkbox',
                'label' => 'Include multi-period specimen example?',
            ));?>
            <small>
                Include an example showing how to model specimens which have more than one phase of
                storage temperature information (e.g. buried in-situ then excavated and stored in a
                museum for a number of years)
            </small>
        </div>
        <div class="grid_5 alpha">
            <?=$this->Form->input('Spreadsheet.custom_kinetics_cols', array (
                'type' => 'checkbox',
                'label' => 'Include columns for customised kinetics values?',
            ));?>
            <small>
                You can use any number of kinetics parameters in a spreadsheet. It is ok to leave this
                blank as parameters from our database can be used. You can choose which on the next screen.
            </small>
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


