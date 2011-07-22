<?php echo $this->Form->create('Specimen', array('id' => 'SpecimenForm', 'url' => $this->here)); ?>
	<fieldset>
 		<legend><?php __('Specimen'); ?></legend>
        
        <?php
            echo $this->Form->input('Specimen.code', array('label' => 'ID:'));
            echo $this->Form->input('Specimen.name', array('label' => 'Name:'));
            echo $this->Form->input('Specimen.description', array('label' => 'Description:'));
        ?>
        
    </fieldset>



        <div id="wizardBottomBar" class="ui-corner-bottom ui-state-default clearfix">
            <div id="wizardProgressButtons" class="clearfix grid_8 alpha">
                <?php echo $this->Form->submit('Continue &raquo;', array('div' => false, 'class' => 'fg-button ui-corner-all ui-state-default ui-priority-primary', 'escape' => false)); ?>
                <?php echo $this->Form->submit('Cancel', array('name' => 'Cancel', 'div' => false, 'class' => 'ui-corner-all ui-state-default fg-button ui-priority-secondary')); ?>
            </div>
            <div id="wizardProgressBar" class="clearfix grid_4 omega">
                dd
            </div>
        </div>
<?php echo $this->Form->end(); ?>