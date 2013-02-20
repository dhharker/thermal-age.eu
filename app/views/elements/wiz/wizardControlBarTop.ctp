<!-- start wizard control bar -->
<?php

$wizardInfos['progress'] = (isset ($wizardInfos)) ? sprintf ('%01.0f', $wizardInfos['progress']) : 0;

?>
<div id="wizardTopBar" class="ui-corner-top ui-state-default clearfix" style="margin: -1px -1px 0 -1px;">
    <div id="wizardTopBarButtons" class="clearfix alpha no-v-margin">
        <div class="paddedCell_5">

            <?php
            $cocl = "ui-corner-left";
            if ($wizardInfos['prevstep'] != FALSE) {
                echo $this->Html->link(
                    '&laquo; Previous',
                    array ('controller' => 'wiz', 'action' => '', $wizardInfos['prevstep']),
                    array('class' => 'fg-button ui-corner-all ui-state-default', 'escape' => false));
                $cocl = '';
            }
            
            echo $this->Html->link(
                    'Continue &raquo;',
                    '',
                    array('class' => 'fg-button ui-corner-all ui-state-default ', 'id' => 'wtbbContinue', 'style' => 'margin-bottom: .35em; float: right;', 'escape' => false));
            ?>
        </div>
    </div>

</div>

<script type="text/javascript">

    (function ($) {
        $('#wtbbContinue').click(function () {
            $('form', '#wizardContainer').first().submit();
            return false;
        });
    } (jQuery));

</script>

<!-- end wizard control bar -->