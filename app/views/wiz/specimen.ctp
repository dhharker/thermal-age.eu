<?php echo $this->Form->create('Specimen', array('id' => 'SpecimenForm', 'url' => $this->here)); ?>
	<fieldset>
 		<legend><?php __('Specimen'); ?></legend>
        
        
<?= $this->Form->input('Specimen.code', array('label' => 'ID:'));?>
<?= $this->Form->input('Specimen.name', array('label' => 'Name:'));?>
<?= $this->Form->input('Specimen.description', array('label' => 'Description:'));?>
        
        
    </fieldset>



        <div id="wizardBottomBar" class="ui-corner-bottom ui-state-default clearfix">
            <div id="wizardProgressButtons" class="clearfix grid_8 alpha">
                <div class="paddedCell_5">

                    <?php echo $this->Html->link(
                        '&laquo; Previous',
                        array ('controller' => 'pages', 'action' => 'help', 'curator_intro'),
                        array('class' => 'fg-button ui-corner-all ui-state-default ui-priority-secondary', 'escape' => false)); ?>

                    <?php echo $this->Form->submit('Cancel', array('name' => 'Cancel', 'div' => false, 'class' => 'fg-button ui-corner-all ui-state-default ui-priority-secondary ui-margin-match-primary')); ?>
                    <?php echo $this->Form->submit('Continue &raquo;', array('div' => false, 'class' => 'fg-button ui-corner-all ui-state-default ui-priority-primary', 'escape' => false)); ?>
                    
                </div>
            </div>
            <div id="wizardProgressBar" class="clearfix grid_4 omega">
                <div style="margin-left:-3em; padding-right: 5px; float: right;">
                    52%
                </div>
                <div id="wpbContainer" class=""></div>
            </div>
        </div>
<?php echo $this->Form->end(); ?>