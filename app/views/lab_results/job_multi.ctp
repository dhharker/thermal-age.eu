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
            Have you or do you intend to attempt DNA extraction from any of these specimens? Help us
            refine the thermal age model by letting us know your results. They will be kept
            confidential unless you choose to make them public.
        </p>
    </div>
    <br />
    <?php
    echo $this->Element ('lab_results/form_multi_spreadsheet', compact('showForm', 'job'));
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
        <h1 class="sbHeading"><?=$this->Icons->i('&#xe003;');?>&ensp;Experimental Results</h1>
        
        <div class="expand-embeds">
            <img src="<?=$this->Html->url(array(
                    'controller' => 'lab_results',
                    'action' => 'regression',
                    $job['Job']['id'],
                    'graph.svg'
                ));?>" style="max-width: 100%; display: block; margin: 0px auto;" />
            <?php
            if (0) {
                $this->addScript ($this->Html->script ('jquery.svg'));
                $this->addScript ($this->Html->script ('job_report'));
                ?>
                <embed src="<?=$this->Html->url(array(
                    'controller' => 'lab_results',
                    'action' => 'regression',
                    $job['Job']['id']
                ));?>" style="width: 8.5px; height: 5.2px; visibility: hidden; display: none;" />
                <?php
                if ($isAjax) {
                    ?>
                    <script type="text/javascript">
                        (function ($) {
                            if (!!window.fnInitSvgs)
                                window.fnInitSvgs();
                        }(jQuery));
                    </script>
                    <?php
                }
            }
            else {
                
            }
            //<?=$this->Element ('loading_spinner', array ('wide' => true))
            ?>
        </div>
        
        <?php
        $rts = array ();
        $willShowForm = true;
        
        if (!$isAjax) {
            echo "<div class=\"ui-helper-clearfix\"></div><ul class=\"noPad objectList box\">";

            $canAddMoreIf = array ('run','will_run');

            foreach ($labResults as $labResult) {
                //if (!in_array ($labResult['LabResult']['result_type'],$canAddMoreIf))
                //    $willShowForm = false;
                $rts[$labResult['LabResult']['result_type']] = 1;
                echo "<li>";
                echo $this->Element ('lab_results/resultlist_item', compact ('labResult'));
                echo "</li>";
            }
            echo "</ul>";
        }
        else {
            $willShowForm = false;
            ?>
            <p>
                <?=count ($labResults) ?> Lab Results have been uploaded for this job. The view these
                all on one page please click below.
            </p>
            <?php
            echo $this->Html->link(
                $this->Icons->i('&#xe04b;'). "&ensp; View Lab Results",
                array (
                    'controller' => 'lab_results',
                    'action' => 'job_multi',
                    $job['Job']['id']
                ),
                array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false));
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
        
    echo "</div>";
    
    $othersResultsTypes = array_keys($rts);
    if (!!$willShowForm && $showForm) {
        echo "<br />" . $this->Element ('lab_results/form_multi_spreadsheet', compact('othersResultsTypes'));
    }
        
}
?>
        
        <?php
            
        ?>
</div>
