
<?php
//debug ($job);

$this->addScript ($this->Html->script ('jquery.svg'));
$this->addScript ($this->Html->script ('job_report'));

if (isset ($job['Job']['id']) && is_numeric ($job['Job']['id'])) {
    $jid = $job['Job']['id'];
}
else {
    $jid = 0;
}

if ($jid == 0) {
    $this->error (404, 'Error', "Job not found.");
}


$show = false;
if (isset ($job['Job']['status']) && $job['Job']['status'] == 3) {
    $this->error (500, 'Error', "Unfortunately the job has crashed.");
}
elseif (!is_array ($results) || empty ($results['output_csv_url'])) {
    $this->error (500, 'Error', "Couldn't find results - job is probably one of not started, not finished or not successful.");
    debug ($results);
}
else
    $show = true;

/*
?>
<div class="grid_12 ">
    <div class="smartbox">
        <?php
        ?>
    </div>
</div>
<?php
*/
if (!!$show) {
    ?>
    <div class="grid_12 ">
        <div class="smartbox">
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
        </div>
    </div>

    <?php
    echo $this->Element ('jobs/publication_info', compact('job'));
    ?>


    <div id="LabResultsScope">
        
    </div>
    <script type="text/javascript">
        (function($){
            $(document).ready(function(){
                $.ajax({
                    url: '<?=$this->Html->url(array (
                        'controller' => 'lab_results',
                        'action' => 'job_multi',
                        $jid
                    ));?>?_ts=<?=microtime(1)?>',
                    success: function (data,xhr) {
                        $('#LabResultsScope').html(data);
                    }
                });
            })
        }(jQuery));
    </script>
    <?php
}
else {
    ?>
        
    <div class="grid_12">
        <div class="smartbox">
            <p>
                <?=@$status['statusText']?> 
                You can download the debugging output (please include this if reporting a problem) here:
                <?=$this->Html->link('Download Job Status Log', array ('controller'=>'jobs','action'=>'report_files',$jid,'status'));?>.
            </p>
        </div>
    </div>
    
        <?php
}
?>

<div class="ui-helper-clearfix"></div>