<div class="soils view">
<h2><?php  __('Soil');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $soil['Soil']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Name'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $soil['Soil']['name']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Thermal Diffusivity M2 Day'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $soil['Soil']['thermal_diffusivity_m2_day']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Particle Size'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $soil['Soil']['particle_size']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Water Content'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $soil['Soil']['water_content']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Citation'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($soil['Citation']['name'], array('controller' => 'citations', 'action' => 'view', $soil['Citation']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('User'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($soil['User']['name'], array('controller' => 'users', 'action' => 'view', $soil['User']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Soil', true), array('action' => 'edit', $soil['Soil']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Soil', true), array('action' => 'delete', $soil['Soil']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $soil['Soil']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Soils', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Soil', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Layers', true), array('controller' => 'layers', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Layer', true), array('controller' => 'layers', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Layers');?></h3>
	<?php if (!empty($soil['Layer'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Sudden'); ?></th>
		<th><?php __('Soil Id'); ?></th>
		<th><?php __('Thickness M'); ?></th>
		<th><?php __('Order'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($soil['Layer'] as $layer):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $layer['id'];?></td>
			<td><?php echo $layer['sudden'];?></td>
			<td><?php echo $layer['soil_id'];?></td>
			<td><?php echo $layer['thickness_m'];?></td>
			<td><?php echo $layer['order'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'layers', 'action' => 'view', $layer['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'layers', 'action' => 'edit', $layer['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'layers', 'action' => 'delete', $layer['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $layer['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Layer', true), array('controller' => 'layers', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
