<div class="users view">
<h2><?php  __('User');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Username'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['username']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Password'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['password']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Alias'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['alias']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Email Priv'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['email_priv']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Url'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['url']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Institution'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['institution']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Bio'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['bio']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $user['User']['modified']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Group'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($user['Group']['name'], array('controller' => 'groups', 'action' => 'view', $user['Group']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit User', true), array('action' => 'edit', $user['User']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete User', true), array('action' => 'delete', $user['User']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $user['User']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Groups', true), array('controller' => 'groups', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Group', true), array('controller' => 'groups', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Reactions', true), array('controller' => 'reactions', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Reaction', true), array('controller' => 'reactions', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Site', true), array('controller' => 'sites', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Soils', true), array('controller' => 'soils', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Soil', true), array('controller' => 'soils', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Temporothermals', true), array('controller' => 'temporothermals', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Temporothermal', true), array('controller' => 'temporothermals', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Uploads', true), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload', true), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Citations');?></h3>
	<?php if (!empty($user['Citation'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Type'); ?></th>
		<th><?php __('Url'); ?></th>
		<th><?php __('Description'); ?></th>
		<th><?php __('Doi'); ?></th>
		<th><?php __('User Id'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Citation'] as $citation):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $citation['id'];?></td>
			<td><?php echo $citation['name'];?></td>
			<td><?php echo $citation['type'];?></td>
			<td><?php echo $citation['url'];?></td>
			<td><?php echo $citation['description'];?></td>
			<td><?php echo $citation['doi'];?></td>
			<td><?php echo $citation['user_id'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'citations', 'action' => 'view', $citation['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'citations', 'action' => 'edit', $citation['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'citations', 'action' => 'delete', $citation['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $citation['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Reactions');?></h3>
	<?php if (!empty($user['Reaction'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Molecule Name'); ?></th>
		<th><?php __('Reaction Name'); ?></th>
		<th><?php __('Substrate Name'); ?></th>
		<th><?php __('Ea Kj Per Mol'); ?></th>
		<th><?php __('F Sec'); ?></th>
		<th><?php __('User Id'); ?></th>
		<th><?php __('Citation Id'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Reaction'] as $reaction):
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
			<td><?php echo $reaction['substrate_name'];?></td>
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
	<?php if (!empty($user['Site'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Lat Dec'); ?></th>
		<th><?php __('Lon Dec'); ?></th>
		<th><?php __('User Id'); ?></th>
		<th><?php __('Citation Id'); ?></th>
		<th><?php __('Description'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Site'] as $site):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $site['id'];?></td>
			<td><?php echo $site['name'];?></td>
			<td><?php echo $site['lat_dec'];?></td>
			<td><?php echo $site['lon_dec'];?></td>
			<td><?php echo $site['user_id'];?></td>
			<td><?php echo $site['citation_id'];?></td>
			<td><?php echo $site['description'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'sites', 'action' => 'view', $site['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'sites', 'action' => 'edit', $site['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'sites', 'action' => 'delete', $site['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $site['id'])); ?>
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
	<h3><?php __('Related Soils');?></h3>
	<?php if (!empty($user['Soil'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Thermal Diffusivity M2 Day'); ?></th>
		<th><?php __('Particle Size'); ?></th>
		<th><?php __('Water Content'); ?></th>
		<th><?php __('Citation Id'); ?></th>
		<th><?php __('User Id'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($user['Soil'] as $soil):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $soil['id'];?></td>
			<td><?php echo $soil['name'];?></td>
			<td><?php echo $soil['thermal_diffusivity_m2_day'];?></td>
			<td><?php echo $soil['particle_size'];?></td>
			<td><?php echo $soil['water_content'];?></td>
			<td><?php echo $soil['citation_id'];?></td>
			<td><?php echo $soil['user_id'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'soils', 'action' => 'view', $soil['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'soils', 'action' => 'edit', $soil['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'soils', 'action' => 'delete', $soil['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $soil['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Soil', true), array('controller' => 'soils', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
<div class="related">
	<h3><?php __('Related Temporothermals');?></h3>
	<?php if (!empty($user['Temporothermal'])):?>
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
		foreach ($user['Temporothermal'] as $temporothermal):
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
<div class="related">
	<h3><?php __('Related Uploads');?></h3>
	<?php if (!empty($user['Upload'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Name'); ?></th>
		<th><?php __('Title'); ?></th>
		<th><?php __('Size'); ?></th>
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
		foreach ($user['Upload'] as $upload):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $upload['id'];?></td>
			<td><?php echo $upload['name'];?></td>
			<td><?php echo $upload['title'];?></td>
			<td><?php echo $upload['size'];?></td>
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
