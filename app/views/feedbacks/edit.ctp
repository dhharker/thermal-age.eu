<div class="feedbacks form">
<?php echo $this->Form->create('Feedback');?>
	<fieldset>
 		<legend><?php __('Edit Feedback'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('title');
		echo $this->Form->input('uri');
		echo $this->Form->input('body');
		echo $this->Form->input('client_info');
		echo $this->Form->input('email');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Feedback.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Feedback.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Feedbacks', true), array('action' => 'index'));?></li>
	</ul>
</div>