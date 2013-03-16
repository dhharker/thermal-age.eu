<div class="labResults form">
<?php echo $this->Form->create('LabResult');?>
	<fieldset>
		<legend><?php __('Edit Lab Result'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('user_id');
		echo $this->Form->input('experiment_type');
		echo $this->Form->input('htp_mfl_less_contaminants');
		echo $this->Form->input('pcr_tgt_length');
		echo $this->Form->input('pcr_num_runs');
		echo $this->Form->input('pcr_num_successes');
		echo $this->Form->input('job_id');
		echo $this->Form->input('notes');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('LabResult.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('LabResult.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Lab Results', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Jobs', true), array('controller' => 'jobs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Job', true), array('controller' => 'jobs', 'action' => 'add')); ?> </li>
	</ul>
</div>