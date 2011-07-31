
<div id="wizardDetailColumnInner" style="clear: both;">
    <ol>
        <?php
        if (isset ($wizardInfos) && is_array ($wizardInfos) && isset ($wizardInfos['steps']))
            $firstCorner = " ui-corner-tl";
            foreach ($wizardInfos['steps'][$wizardInfos['wizardname']] as $stepName => $stepInfo) {
        ?>
        <li>
            <div class="progressStep">
                <a href="<?=$this->Html->url (array ('controller'=>'wiz', 'action' => $wizardInfos['wizardname'], $stepName))?>"
                   class="ui-ish<?=$firstCorner?> <?=$stepInfo['class']?>">
                    <img src="/img/wiz_step_<?=$stepInfo['class']?>.png" alt="complete" />
                    <span class="stepName"><?=$stepInfo['title']?></span>
                    <?php if ($stepInfo['sfval']) { ?>
                    <span class="stepDetail"><?=print_r ($stepInfo['sfval'], TRUE)?></span>
                    <?php } ?>
                </a>
            </div>
        </li>
        <?php
                $firstCorner = '';
            }
        ?>
    </ol>
</div>