<?php
if (isset ($job['Job']['id']) && is_numeric ($job['Job']['id'])) {
    $jid = $job['Job']['id'];
}
else {
    $jid = 0;
}

if ($jid == 0) {
    $this->error (404, 'Error', "Job not found.");
}
elseif (!@$async) {

?>
<div class="grid_7">
    <div class="smartbox">
        
        <div class="floatingLoader"></div>
        <h2 class="sbHeading">Calculator Output</h2>
        
        <?=$this->Element ('loading_spinner');?>
        <div id="ajax-job-status">
            <p>
                Stand by for status update...
            </p>
        </div>
    </div>
</div>
<script type="text/javascript">
    (function ($){
        $(document).ready(function () {
            var $container = $('#ajax-job-status');
            var $loading = $('.floatingLoader',$container.parent()).first();
            useful.ajaxReloader($container,'<?=$this->Html->url(array('controller' => 'jobs', 'action' => 'status', $jid))?>',{
                sinceEpoch: <?=time()?>,
                onLoading: function () {
                    $loading.hide().html('<img src="/img/loading_spinner_2.gif" alt="loading..." />').show('fade');
                },
                onComplete: function () {
                    $loading.hide('fade');
                }
            });
        });
    }(jQuery));
</script>



<div class="grid_5">
    <div class="smartbox">
        <div class="floatingLoader"></div>
        <h2 class="sbHeading">System Status</h2>
        <div id="ajax-system-status"><p>Updating...</p></div>
        <script type="text/javascript">
            (function ($){
                $(document).ready(function () {
                    var $container = $('#ajax-system-status');
                    var $loading = $('.floatingLoader',$container.parent()).first();
                    useful.ajaxReloader($container,'<?=$this->Html->url(array('controller' => 'jobs', 'action' => 'system'))?>',{
                        sinceEpoch: <?=time()?>,
                        onLoading: function () {
                            $loading.hide().html('<img src="/img/loading_spinner_2.gif" alt="loading..." />').show('fade');
                        },
                        onComplete: function () {
                            $loading.hide('fade');
                        }
                    });
                });
            }(jQuery));
        </script>
    </div>
    
</div>


<?php

}
else {
    ?>
        <p>
            <?=$status['statusText']?>
        </p>
        <p>
            <?=nl2br(@substr($status['statusFile'],0,1000))?>
        </p>
    <?php
}
?>






