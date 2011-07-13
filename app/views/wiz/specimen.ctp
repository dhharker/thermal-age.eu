<?php echo $this->Form->create('Specimen', array('id' => 'SignupForm', 'url' => $this->here)); ?>
	<h2>Step 1: Account Information</h2>
	<?php
		echo $this->Form->input('Client.first_name', array('label' => 'First Name:'));
		echo $this->Form->input('Client.last_name', array('label' => 'Last Name:'));
		echo $this->Form->input('Client.phone', array('label' => 'Phone Number:'));
		echo $this->Form->input('User.email', array('label'=>'Email:'));
		echo $this->Form->input('User.password',array('label'=>'Password:'));
		echo $this->Form->input('User.confirm', array('label'=>'Confirm:', 'type'=>'password'));
	?>
	<div class="submit">
		<?php echo $this->Form->submit('Continue', array('div' => false)); ?>
		<?php echo $this->Form->submit('Cancel', array('name' => 'Cancel', 'div' => false)); ?>
	</div>
<?php echo $this->Form->end(); ?>