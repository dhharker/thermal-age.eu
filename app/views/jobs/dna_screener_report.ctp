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

$this->addScript ($this->Html->script ('jquery.svg'));
$this->addScript ($this->Html->css ('jquery.svg.min'));
$this->addScript ($this->Html->css ('jquery.svganim.min'));
$this->addScript ($this->Html->script ('job_report'));

//echo $results['graphs']['lambda'];

if (!isset ($results['summary']['λ'])) {
    $this->error (404, 'Error', "Job has failed to complete properly.");
}
else {
    ?>

    <div class="grid_5">
        <div class="grid_5 alpha">
            <?=$this->Element ('jobs/traffic_lights', array ('λ' => $results['summary']['λ'], 'class' => 'smartbox'));?>
        </div>
    </div>

    <div class="grid_7">
        <div class="smartbox">
            <div class="expand-embeds">
                <?php

                if (!empty ($results['graphs']['lambda'])) {
                    ?>
                    <embed src="/<?=$results['graphs']['lambda']?>" style="width: 8.5px; height: 5.2px; visibility: hidden; display: none;" />
                    <?php
                }
                if ($results['summary']['λ'] > 1) {
                    ?>
                    <p><strong>Attention:</strong> Lambda values above 1 cause this graph to freak out and not draw properly.
                    Fortunately, since your sample is so badly degraded, a broken graph is the least of your worries!</p>
                    <?php
                }
                ?>
            </div>
            <?=$this->Element ('loading_spinner', array ('wide' => true))?>
        </div>
    </div>
    <div class="grid_12">
        <div class="smartbox">
            <div class="expand-embeds">
                <?php
                if (!empty ($results['graphs']['burial'])) {
                    ?>
                    <embed src="/<?=$results['graphs']['burial']?>" style="width: 8.5px; height: 6.7px; visibility: hidden; display: none;" />
                    <?php
                }
                ?>
            </div>
            <?=$this->Element ('loading_spinner', array ('wide' => true))?>
        </div>
    </div>

    <div class="grid_5">
        <div class="smartbox">
            <div id="">
                <pre><?=print_r ($results, true)?></pre>
                <pre><?=print_r ($job, true)?></pre>
            </div>
        </div>
    </div>

    <div class="grid_7">
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
<?php
}
?>