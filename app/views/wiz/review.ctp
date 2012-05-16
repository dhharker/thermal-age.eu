<h1 class="sbHeading ui-corner-tl">
    Review
</h1>


<?php echo $this->Form->create('Job', array('id' => 'JobForm', 'url' => $this->here)); ?>
	<fieldset>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Job.processor_name', array (
                'type' => 'hidden',
                'default' => 'thermal_age',
                //'disabled' => true
            ));?>
        </div>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Job.parser_name', array (
                'type' => 'hidden',
                'default' => 'dna_screener',
                //'disabled' => true
            ));?>
        </div>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Job.reporter_name', array (
                'type' => 'hidden',
                'default' => 'dna_screener',
                //'disabled' => true
            ));?>
        </div>
        
    </fieldset>


<p>
    All done - click 'Continue' below to add your job to the queue, it will be processed in due turn
    and will take anywhere up to a few minutes to complete. 
</p>


<ol style="display: none">
<?php

    foreach ($input as $stepName => $models) {
        echo "<li>$stepName:<ol>";
        foreach ($models as $modelName => $modelValues) {
            echo "<li>$modelName:<ol>";
            foreach ($modelValues as $fieldName => $fieldValue) {
                echo "<li><strong>$fieldName</strong>: " . ((is_string($fieldValue)) ? $fieldValue : print_r ($fieldValue, TRUE)) . "</li>";
            }
            echo "</ol></li>";
        }
        echo "</ol></li>";
    }

?>
</ol>


<?=$this->element('wiz/wizardControlBar',  array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(document).ready (function () {
    wc.initReviewForm ();
});
</script>



