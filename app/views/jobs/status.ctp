<?php
if (isset ($job['Job']['id']) && is_numeric ($job['Job']['id'])) {
    $jid = $job['Job']['id'];
}
else {
    $jid = 0;
}
if (!@$async) {
?><div class="grid_12">
    <div class="smartbox">
        <div style="width: 50px; text-align: center; margin: 0px; float: left; margin: 1em;">
            <img src="/img/loading_spinner_blue.gif" alt="Please wait..." />
        </div>
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
            $('#jobStatusContainer').load ('/jobs/status/' + <?=$jid?>, function () {

            });
        };
        var inter = setInterval (upd, 5000);
    });
</script>
<?php

}
else {
    ?>
<p>
    <? print_r ($status)?>
</p>
    <?php
}
?>