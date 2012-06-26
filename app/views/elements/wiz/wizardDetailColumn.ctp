<?php
$this->addScript ($this->Html->script ('keepalive'));
?>
<div id="wizardDetailColumn" class="grid_4 omega ui-ish ui-corner-tl">
    - loading -
</div>

<script type="text/javascript">
    wc.initProgressColumn ("<?=$this->Html->url (array ('controller' => 'wiz', 'action' => 'progress', $this->action)) ?>");
</script>