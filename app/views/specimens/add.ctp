<div class="specimens form">
<?php echo $this->Form->create('Specimen');?>
	<fieldset>
 		<legend><?php __('Add Specimen'); ?></legend>
	<?php
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

		<li><?php echo $this->Html->link(__('List Specimens', true), array('action' => 'index'));?></li>
	</ul>
</div>