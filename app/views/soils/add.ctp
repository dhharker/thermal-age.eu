<div class="soils form">
<?php echo $this->Form->create('Soil');?>
	<fieldset>
 		<legend><?php __('Add Soil'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('thermal_diffusivity_m2_sec');
		echo $this->Form->input('particle_size');
		echo $this->Form->input('water_content');
		echo $this->Form->input('citation_id');
		echo $this->Form->input('user_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Soils', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>