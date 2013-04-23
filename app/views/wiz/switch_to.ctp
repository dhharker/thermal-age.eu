<div class="grid_12"><div class="smartbox">
    <h1 class="sbHeading"><?=$this->Icons->i('&#xe065;')?> Current Wizard Run is Incomplete!</h1>
    <p>
        
    </p>
    <div>
        <?php
        $nw = $this->getVar('newWizard');
        ?>
        <?php echo $this->Html->link(
            $this->Icons->i('&#xe073;') . "&ensp; Save incomplete wizard run for later",
            array ('controller' => 'wiz', 'action' => 'switch_to', $nw, '?' => array ('action' => 'save')),
            array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
        <?php echo $this->Html->link(
            $this->Icons->i('&#xe022;') . "&ensp; Discard incomplete wizard run",
            array ('controller' => 'wiz', 'action' => 'switch_to', $nw, '?' => array ('action' => 'discard')),
            array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
        <?php echo $this->Html->link(
            $this->Icons->i('&#xe052;') . "&ensp; Cancel and return to current wizard",
            array ('controller' => 'wiz', 'action' => 'switch_to', $nw, '?' => array ('action' => 'cancel')),
            array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
    </div>
</div></div>