<div class="reactions form">
<?php echo $this->Form->create('Reaction');?>
	<fieldset>
 		<legend><?php __('Edit Reaction'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('molecule_name');
		echo $this->Form->input('reaction_name');
		echo $this->Form->input('ea_kj_per_mol');
		echo $this->Form->input('f_sec');
		echo $this->Form->input('user_id');
		echo $this->Form->input('citation_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Reaction.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Reaction.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Reactions', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
	</ul>
</div>