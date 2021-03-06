<?php
/*
 * Optional:
 *  $showForm   bool show form or not default true
 */
if (!isset ($showForm)) $showForm = true;
?>
<div class="grid_12" id="LabResultsScope">
<?php
if (!isset ($labResults) || empty ($labResults) && $showForm) {
?>
    <div class="smartbox">
        <h2 class="sbHeading">Important - Please help!</h2>
        <p>
            Have you or do you intend to attempt DNA extraction from this specimen?
            Please let us know whether your experiment was a success so we can refine the thermal age model.
            Your results will be kept confidential. Please select an option:
        </p>
        <?php
        echo $this->Element ('lab_results/form', compact('showForm'));
        ?>
<?php
}
elseif (!isset ($labResults) || empty ($labResults) && !$showForm) {
    // No results and we don't own the job
    ?>
    <div class="smartbox">
        <h2 class="sbHeading"><?=$this->Icons->i('&#xe003;');?>&ensp;No Experimental Results Available</h2>
    </div>
    <?php
}
else {
    ?>
    <div class="smartbox">
        <h2 class="sbHeading"><?=$this->Icons->i('&#xe003;');?>&ensp;Experimental Results</h2>
        
        <?php
        
        echo "<div class=\"ui-helper-clearfix\"></div><ul class=\"noPad objectList box\">";
        
        $willShowForm = true;
        $canAddMoreIf = array ('run','will_run');
        $rts = array ();
        
        foreach ($labResults as $labResult) {
            if (!in_array ($labResult['LabResult']['result_type'],$canAddMoreIf))
                $willShowForm = false;
            $rts[$labResult['LabResult']['result_type']] = 1;
            echo "<li>";
            echo $this->Element ('lab_results/resultlist_item', compact ('labResult'));
            echo "</li>";
        }
        echo "</ul>";
        $othersResultsTypes = array_keys($rts);
        if (!!$willShowForm && $showForm) {
            //echo "<li>";
            echo $this->Element ('lab_results/form', compact('othersResultsTypes'));
            //echo "</li>";
        }
        if (!!$showForm)
            echo $this->Html->link(
                $this->Icons->i('&#xe020;'). "&ensp; Publish any unpublished Lab Results immediately",
                array (
                    'controller' => 'jobs',
                    'action' => 'publish_results',
                    $job['Job']['id']
                ),
                array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false))
            ;
}
?>
        
        <?php
            
        ?>
    </div>
</div>
