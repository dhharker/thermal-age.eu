<div class="temporothermals form">
<?php echo $this->Form->create('Temporothermal');?>
	<fieldset>
 		<legend><?php __('Add Temporothermal'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('temp_mean_c');
		echo $this->Form->input('temp_pp_amp_c');
		echo $this->Form->input('upload_id');
		echo $this->Form->input('startdate_ybp');
		echo $this->Form->input('stopdate_ybp');
		echo $this->Form->input('range_years');
		echo $this->Form->input('user_id');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Temporothermals', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Uploads', true), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload', true), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>