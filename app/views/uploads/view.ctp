<div class="uploads view">
<h2><?php  __('Upload');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $upload['Upload']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $upload['Upload']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Title'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $upload['Upload']['title']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Size'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $upload['Upload']['size']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Mime Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $upload['Upload']['mime_type']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $upload['Upload']['description']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Citation'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($upload['Citation']['name'], array('controller' => 'citations', 'action' => 'view', $upload['Citation']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($upload['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('File Contents'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $upload['Upload']['file_contents']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('File Location'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $upload['Upload']['file_location']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Upload', true), array('action' => 'edit', $upload['Upload']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Upload', true), array('action' => 'delete', $upload['Upload']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $upload['Upload']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Uploads', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Temporothermals', true), array('controller' => 'temporothermals', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Temporothermal', true), array('controller' => 'temporothermals', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Temporothermals');?></h3>
	<?php if (!empty($upload['Temporothermal'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Temp Mean C'); ?></th>
		<th><?php __('Temp Pp Amp C'); ?></th>
		<th><?php __('Upload Id'); ?></th>
		<th><?php __('Startdate Ybp'); ?></th>
		<th><?php __('Stopdate Ybp'); ?></th>
		<th><?php __('Range Years'); ?></th>
		<th><?php __('User Id'); ?></th>
		<th><?php __('Description'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($upload['Temporothermal'] as $temporothermal):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $temporothermal['id'];?></td>
			<td><?php echo $temporothermal['name'];?></td>
			<td><?php echo $temporothermal['temp_mean_c'];?></td>
			<td><?php echo $temporothermal['temp_pp_amp_c'];?></td>
			<td><?php echo $temporothermal['upload_id'];?></td>
			<td><?php echo $temporothermal['startdate_ybp'];?></td>
			<td><?php echo $temporothermal['stopdate_ybp'];?></td>
			<td><?php echo $temporothermal['range_years'];?></td>
			<td><?php echo $temporothermal['user_id'];?></td>
			<td><?php echo $temporothermal['description'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'temporothermals', 'action' => 'view', $temporothermal['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'temporothermals', 'action' => 'edit', $temporothermal['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'temporothermals', 'action' => 'delete', $temporothermal['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $temporothermal['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Temporothermal', true), array('controller' => 'temporothermals', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
