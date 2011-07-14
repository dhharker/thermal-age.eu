<?php echo $this->Form->create('Specimen', array('id' => 'SignupForm', 'url' => $this->here)); ?>
	<h2>Specimen Details</h2>
	<?php
		echo $this->Form->input('Specimen.code', array('label' => 'ID:'));
		echo $this->Form->input('Specimen.name', array('label' => 'Name:'));
		echo $this->Form->input('Specimen.description', array('label' => 'Description:'));
	?>
	<div class="submit">
		<?php echo $this->Form->submit('Continue', array('div' => false)); ?>
		<?php echo $this->Form->submit('Cancel', array('name' => 'Cancel', 'div' => false)); ?>
	</div>
<?php echo $this->Form->end(); ?>