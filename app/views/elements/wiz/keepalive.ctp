<?php
$this->addScript ($this->Html->script ('keepalive'));
?>
<div id="keep-alive-timer">
    <span><?=sprintf ("Session length (to last keep-alive ping): %s", $this->getVar('kept_alive_for'));?></span>
</div>