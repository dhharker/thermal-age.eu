<div class="specimens view">
<h2><?php  __('Specimen');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $specimen['Specimen']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $specimen['Specimen']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Code'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $specimen['Specimen']['code']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $specimen['Specimen']['description']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Specimen', true), array('action' => 'edit', $specimen['Specimen']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Specimen', true), array('action' => 'delete', $specimen['Specimen']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $specimen['Specimen']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Specimens', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Specimen', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
