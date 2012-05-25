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
<div class="grid_12">
    <div class="smartbox">
        <?=$this->Element ('loading_spinner');?>
        <div id="jobStatusContainer">
            <p>
                Stand by for status update...
            </p>
        </div>
    </div>

</div>

<script type="text/javascript">
    $(function () {
        var upd = function () {
            var sc = $('#jobStatusContainer');
            if  (sc.data('wait') != true)
            sc
                .data('wait',true)
                .load ('/jobs/status/' + <?=$jid;?>, function () {
                    sc.data('wait', false);
                });
        };
        var inter = setInterval (upd, 2000);
        upd();
    });
</script>

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