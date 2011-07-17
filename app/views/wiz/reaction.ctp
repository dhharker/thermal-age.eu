<div class="reactions form">
<?php echo $this->Form->create('Reaction', array('id' => 'SpecimenForm', 'url' => $this->here)); ?>
	<fieldset>
 		<legend><?php __('Edit Reaction'); ?></legend>
	<?php
		echo $this->Form->input('Reaction.id');
		echo $this->Form->input('Reaction.name');
		echo $this->Form->input('Reaction.molecule_name');
		echo $this->Form->input('Reaction.reaction_name');
		echo $this->Form->input('Reaction.ea_kj_per_mol');
		echo $this->Form->input('Reaction.f_sec');
		echo $this->Form->input('Reaction.user_id');
		echo $this->Form->input('Reaction.citation_id');
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