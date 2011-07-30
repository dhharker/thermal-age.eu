<!-- start wizard control bar -->
<?php

$wizardInfos['progress'] = (isset ($wizardInfos)) ? sprintf ('%01.0f', $wizardInfos) : 0;

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


    
    <div id="wizardProgressBarContainer" class="clearfix grid_4 omega no-v-margin">
        <?=$this->element('wiz/wizardDetailColumn', array ('wizardInfos' => $wizardInfos));?>
        <a id="wizardProgressBar" class="ui-corner-br"
           href="#<?$this->Html->url (array ('controller' => 'wiz', 'action' => 'progress')) ?>">

            <div class="progressbarPadding ui-state-default hover ui-corner-br">
                <div style="padding-right: 5px; float: right; text-align: right; font-weight: bold;" class="">
                    <?=$wizardInfos['progress']?>%
                </div>
                <div id="wpbContainer" class=""></div>
            </div>
        </a>
    </div>
</div>

<script type="text/javascript">


$('a#wizardProgressBar').once ('widgetInited', function () {
    $(this)
        .click (function () {
            wc.damocles ($('#wizardDetailColumn'));
            $('#wizardDetailColumn').slideToggle(250, function () {
                $(this).resize();
                wc.damocles ($(this));
            });
            $(this).blur();
            return false;
        })
        .find ('#wpbContainer').progressbar ({
            value: <?=$wizardInfos['progress']?>,
        })
    ;
});


</script>

<!-- end wizard control bar -->