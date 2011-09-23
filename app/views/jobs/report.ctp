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
?>
<div class="grid_12">
    <div class="smartbox">
        <div id="jobStatusContainer">
            <pre>

<?=$results?>
            </pre>
        </div>
    </div>
</div>
