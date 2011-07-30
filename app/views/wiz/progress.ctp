<div id="wizardDetailColumnInner" style="clear: both;">
    <ol>
        <?php
        if (isset ($wizard) && is_array ($wizard) && isset ($wizard['steps']))
            $firstCorner = " ui-corner-tl";
            foreach ($wizard['steps'] as $stepName => $stepInfo) {
        ?>
        <li>
            <a href="<?=$this->Html->url (array ('controller'=>'wiz', 'action' => $wizard['wizardname'], $stepName))?>"
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