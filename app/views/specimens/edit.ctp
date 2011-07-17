<div class="specimens form">
<?php echo $this->Form->create('Specimen');?>
	<fieldset>
 		<legend><?php __('Edit Specimen'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('code');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Specimen.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Specimen.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Specimens', true), array('action' => 'index'));?></li>
	</ul>
</div>