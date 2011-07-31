
<div id="wizardDetailColumnInner" style="clear: both;">
    <ol>
        <?php
        if (isset ($wizardInfos) && is_array ($wizardInfos) && isset ($wizardInfos['steps']))
            $firstCorner = " ui-corner-tl";
            foreach ($wizardInfos['steps'][$wizardInfos['wizardname']] as $stepName => $stepInfo) {
        ?>
        <li>
            <a href="<?=$this->Html->url (array ('controller'=>'wiz', 'action' => $wizardInfos['wizardname'], $stepName))?>"
               class="progressStepLink ui-ish<?=$firstCorner?> <?=$stepInfo['class']?>">
                <img src="/img/wiz_step_<?=$stepInfo['class']?>.png" alt="complete" />
                <?=$stepInfo['title']?>
            </a>
        </li>
        <?php
                $firstCorner = '';
            }
        ?>
    </ol>
</div>