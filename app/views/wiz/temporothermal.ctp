<div class="temporothermals form">
<?php echo $this->Form->create('Temporothermal', array('id' => 'TemporothermalForm', 'url' => $this->here)); ?>
	<fieldset>
 		<legend><?php __('Edit Temporothermal'); ?></legend>
	<?php
		echo $this->Form->input('Temporothermal.id');
		echo $this->Form->input('Temporothermal.name');
		echo $this->Form->input('Temporothermal.temp_mean_c');
		echo $this->Form->input('Temporothermal.temp_pp_amp_c');
		echo $this->Form->input('Temporothermal.upload_id');
		echo $this->Form->input('Temporothermal.startdate_ybp');
		echo $this->Form->input('Temporothermal.stopdate_ybp');
		echo $this->Form->input('Temporothermal.range_years');
		echo $this->Form->input('Temporothermal.user_id');
		echo $this->Form->input('Temporothermal.description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Temporothermal.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Temporothermal.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Temporothermals', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Uploads', true), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload', true), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>