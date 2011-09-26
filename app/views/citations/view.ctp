<div class="citations view">
<h2><?php  __('Citation');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $citation['Citation']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $citation['Citation']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Type'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $citation['Citation']['type']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Url'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $citation['Citation']['url']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Description'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $citation['Citation']['description']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Doi'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $citation['Citation']['doi']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($citation['User']['name'], array('controller' => 'users', 'action' => 'view', $citation['User']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Citation', true), array('action' => 'edit', $citation['Citation']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Citation', true), array('action' => 'delete', $citation['Citation']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $citation['Citation']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Reactions', true), array('controller' => 'reactions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Reaction', true), array('controller' => 'reactions', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Uploads', true), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload', true), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Reactions');?></h3>
	<?php if (!empty($citation['Reaction'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Molecule Name'); ?></th>
		<th><?php __('Reaction Name'); ?></th>
		<th><?php __('Ea Kj Per Mol'); ?></th>
		<th><?php __('F Sec'); ?></th>
		<th><?php __('User Id'); ?></th>
		<th><?php __('Citation Id'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($citation['Reaction'] as $reaction):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $reaction['id'];?></td>
			<td><?php echo $reaction['name'];?></td>
			<td><?php echo $reaction['molecule_name'];?></td>
			<td><?php echo $reaction['reaction_name'];?></td>
			<td><?php echo $reaction['ea_kj_per_mol'];?></td>
			<td><?php echo $reaction['f_sec'];?></td>
			<td><?php echo $reaction['user_id'];?></td>
			<td><?php echo $reaction['citation_id'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'reactions', 'action' => 'view', $reaction['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'reactions', 'action' => 'edit', $reaction['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'reactions', 'action' => 'delete', $reaction['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $reaction['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Reaction', true), array('controller' => 'reactions', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Sites');?></h3>
	<?php if (!empty($citation['Site'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>

Warning: Invalid argument supplied for foreach() in /usr/share/php/cake/console/templates/default/views/view.ctp on line 108
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($citation['Site'] as $site):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>

Warning: Invalid argument supplied for foreach() in /usr/share/php/cake/console/templates/default/views/view.ctp on line 125
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'sites', 'action' => 'view', $site[''])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'sites', 'action' => 'edit', $site[''])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'sites', 'action' => 'delete', $site['']), null, sprintf(__('Are you sure you want to delete # %s?', true), $site[''])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Uploads');?></h3>
	<?php if (!empty($citation['Upload'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('Mime Type'); ?></th>
		<th><?php __('Description'); ?></th>
		<th><?php __('Citation Id'); ?></th>
		<th><?php __('User Id'); ?></th>
		<th><?php __('File Contents'); ?></th>
		<th><?php __('File Location'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($citation['Upload'] as $upload):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $upload['id'];?></td>
			<td><?php echo $upload['name'];?></td>
			<td><?php echo $upload['title'];?></td>
			<td><?php echo $upload['mime_type'];?></td>
			<td><?php echo $upload['description'];?></td>
			<td><?php echo $upload['citation_id'];?></td>
			<td><?php echo $upload['user_id'];?></td>
			<td><?php echo $upload['file_contents'];?></td>
			<td><?php echo $upload['file_location'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'uploads', 'action' => 'view', $upload['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'uploads', 'action' => 'edit', $upload['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'uploads', 'action' => 'delete', $upload['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $upload['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Upload', true), array('controller' => 'uploads', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
