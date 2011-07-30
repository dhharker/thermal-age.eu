<div id="wizardDetailColumnInner" style="clear: both;">
    <ol>
        <?php
        if (isset ($wizard) && is_array ($wizard) && isset ($wizard['steps']))
            foreach ($wizard['steps'] as $stepName => $stepInfo) {
        ?>
        <li>
            <a href="<?=$this->Html->url (array ('controller'=>'wiz', 'action' => $wizard['wizardname'], $stepName))?>">
                <?=$stepInfo['title']?>
            </a>
        </li>
        <?php
            }
        ?>
    </ol>
</div>