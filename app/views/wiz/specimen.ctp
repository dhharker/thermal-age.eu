<?php echo $this->Form->create('Specimen', array('id' => 'SpecimenForm', 'url' => $this->here)); ?>
	<h2 class="sbHeading">Specimen Details</h2>
	<?php
		echo $this->Form->input('Specimen.code', array('label' => 'ID:'));
		echo $this->Form->input('Specimen.name', array('label' => 'Name:'));
		echo $this->Form->input('Specimen.description', array('label' => 'Description:'));
	?>
	<div class="submit">
		
	</div>
<?php echo $this->Form->end(); ?>


    <div id="wizardBottomBar" class="ui-corner-bottom ui-state-default clearfix">
        <div id="wizardProgressButtons">
            <?php echo $this->Form->submit('Continue', array('div' => false)); ?>
            <?php echo $this->Form->submit('Cancel', array('name' => 'Cancel', 'div' => false)); ?>

        </div>
        <div id="wizardProgressBar">

        </div>
    </div>

