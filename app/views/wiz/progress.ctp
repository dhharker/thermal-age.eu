
<div id="wizardDetailColumnInner" style="clear: both;">
    <ol>


        <li>
            <div class=" ui-corner-tl progressStep ui-ish">
                <h2 class="sbHeading"><?=@$wizardInfos['wizardtitle'];?></h2>
            </div>
        </li>


        <?php
        if (isset ($wizardInfos) && is_array ($wizardInfos) && isset ($wizardInfos['steps']))
            $firstCorner = "";
            foreach ($wizardInfos['steps'][$wizardInfos['wizardname']] as $stepName => $stepInfo) {
        ?>
        <li>
            <div class="progressStep ui-ish <?=$stepInfo['class']?>">
                <a href="<?=$this->Html->url (array ('controller'=>'wiz', 'action' => $wizardInfos['wizardname'], $stepName))?>"
                   class="clearfix <?=$firstCorner?> <?=$stepInfo['class']?>">
                    <img src="/img/wiz_step_<?=$stepInfo['class']?>.png" alt="complete" />
                    <span class="stepName"><?=$stepInfo['title']?></span>
                    <div class="stepDetail">
                        <?php
                        if (isset ($stepInfo['sfval']) && strlen (trim($stepInfo['sfval'])) > 0) {
                            echo strip_tags ($stepInfo['sfval']);
                        }
                        elseif ($stepInfo['class'] == 'current') {
                            echo "&laquo; you are here";
                        }
                        elseif ($stepInfo['class'] == 'future') {
                            echo "pending";
                        }
                        else echo "?";

                        ?>
                    </div>
                    <div style="clear: both"></div>
                </a>
            </div>
        </li>
        <?php
                $firstCorner = '';
            }
        ?>
    </ol>
</div>