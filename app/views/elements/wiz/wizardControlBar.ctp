<!-- start wizard control bar -->
<?php

$progress = (isset ($progress)) ? sprintf ('%01.0f', $progress) : 0;

?>
<div id="wizardBottomBar" class="ui-corner-bottom ui-state-default clearfix">
    <div id="wizardProgressButtons" class="clearfix grid_8 alpha no-v-margin">
        <div class="paddedCell_5">

            <?php echo $this->Html->link(
                '&laquo; Previous',
                array ('controller' => 'pages', 'action' => 'help', 'curator_intro'),
                array('class' => 'fg-button ui-corner-left ui-state-default ui-priority-secondary', 'escape' => false)); ?>
            <?php echo $this->Html->link(
                'Cancel',
                array ('controller' => '', 'action' => '', ''),
                array('class' => 'fg-button ui-corner-right ui-state-default ui-priority-secondary', 'escape' => false)); ?>
            <?php echo $this->Form->submit('Continue &raquo;', array('div' => false, 'class' => 'fg-button ui-corner-all ui-state-default', 'escape' => false)); ?>
        </div>
    </div>


    <a id="wizardProgressBar" class="clearfix grid_4 omega no-v-margin ui-corner-br"
       href="<?=$this->Html->url (array ('controller' => 'wiz', 'action' => 'progress')) ?>">
        <div class="progressbarPadding ui-state-default hover ui-corner-br">
            <div style="padding-right: 5px; float: right; text-align: right; font-weight: bold;" class="">
                <?=$progress?>%
            </div>
            <div id="wpbContainer" class=""></div>
        </div>
    </a>
</div>

<script type="text/javascript">
$(document).ready (function () {
    $('#wizardProgressBar').pageSlide ({
        width: '350px',
        direction: 'left'
    }).find ('#wpbContainer').progressbar ({
        value: <?=$progress?>,
    });
});
</script>

<!-- end wizard control bar -->