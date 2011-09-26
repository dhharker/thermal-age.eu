<div class="jobs form">
<?php echo $this->Form->create('Job');?>
	<fieldset>
 		<legend><?php __('Add Job'); ?></legend>
	<?php
		echo $this->Form->input('title');
		echo $this->Form->input('user_id');
		echo $this->Form->input('data');
		echo $this->Form->input('processor_name');
		echo $this->Form->input('parser_name');
		echo $this->Form->input('reporter_name');
		echo $this->Form->input('pub_ref');
		echo $this->Form->input('status');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Jobs', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>