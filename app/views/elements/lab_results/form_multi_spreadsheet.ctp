<?php
$csv_name = $csv_url = '?';
if (isset ($job) && !empty ($job)) {
    $csv_url = array (
        'controller' => 'lab_results',
        'action' => 'csv_results_template',
        $job['Job']['id']
    );
}
?>
<div class="smartbox">
    <h2 class="sbHeading"><?=$this->Icons->i('&#xe003;');?>&ensp;Upload Results</h2>
    <ol>
        <li>Download this template spreadsheet (wizard results plus extra columns for your experimental results)
            <?php echo $this->Html->link(
                $this->Icons->i('&#xe030;'). "&ensp; Download Template Spreadsheet",
                $csv_url,
                array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
        </li>
        <li>Enter your experimental results in the appropriate columns in the template spreadsheet.</li>
        <li>Upload the resulting spreadsheet of your experimental results
            <?php echo $this->Form->create  ('Spreadsheet', array('id' => 'SpreadsheetForm', 'url' => array('controller' => 'lab_results', 'action' => 'csv_results_upload', $job['Job']['id']), 'class' => 'ui-corner-all noAjax', 'type' => 'file')); ?>
            <fieldset>
                
                <div>
                    <?=$this->Form->input('Spreadsheet.file', array(
                        'type' => 'file',
                        'label' => $this->Icons->i('&#xe06e;') .' &nbsp;Upload CSV Data',
                    ));
                    ?>
                    <div>
                        <?=$this->Form->input('LabResult.public', array(
                            'type' => 'checkbox',
                            'checked' => true
                        ));
                        ?>
                        <?=$this->Form->input('LabResult.public_date', array(
                        ));
                        ?>
                    </div>
                    <?=$this->Form->submit('Upload Results &raquo;', array(
                        'style' => 'display: block; width: 99%; padding: 5px 0; margin: 0 auto;',
                        'class' => 'fg-button ui-corner-all ui-state-default cta-button',
                        'escape' => false));
                    ?>
                </div>
            </fieldset>
            <?
            echo $this->Form->end();
        ?>
        </li>
    </ol>
</div>