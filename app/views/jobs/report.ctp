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
        <div id="">
            <embed src="svg_button.svg" width="300" height="100" />
        </div>
    </div>
</div>


<div class="grid_6">
    <div class="smartbox">
        <div id="">
            <pre><?=$results?></pre>
            <pre><?=print_r ($job, true)?></pre>
        </div>
    </div>
</div>
<div class="grid_6">
    <div class="smartbox">
        <div id="">
            <p>
                <?=@$status['statusText']?>
            </p>
            <p>
                <?=nl2br(@$status['statusFile'])?>
            </p>
        </div>
    </div>
</div>
