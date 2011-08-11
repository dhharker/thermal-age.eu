<div class="layers form">
<?php echo $this->Form->create('Layer');?>
	<fieldset>
 		<legend><?php __('Add Layer'); ?></legend>
	<?php
		echo $this->Form->input('sudden');
		echo $this->Form->input('soil_id');
		echo $this->Form->input('thickness_m');
		echo $this->Form->input('order');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Layers', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Soils', true), array('controller' => 'soils', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Soil', true), array('controller' => 'soils', 'action' => 'add')); ?> </li>
	</ul>
</div>