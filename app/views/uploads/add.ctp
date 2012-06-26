<div class="uploads form">
<?php echo $this->Form->create('Upload', array ('type' => 'file'));?>
	<fieldset>
 		<legend><?php __('Add Upload'); ?></legend>
	<?php
		echo $this->Form->file('file');
        echo $this->Form->input('title');
        echo $this->Form->input('citation_id');
        echo $this->Form->input('description');
		
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Uploads', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Temporothermals', true), array('controller' => 'temporothermals', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Temporothermal', true), array('controller' => 'temporothermals', 'action' => 'add')); ?> </li>
	</ul>
</div>