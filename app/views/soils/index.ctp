<div class="soils index">
	<h2><?php __('Soils');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('thermal_diffusivity_m2_sec');?></th>
			<th><?php echo $this->Paginator->sort('particle_size');?></th>
			<th><?php echo $this->Paginator->sort('water_content');?></th>
			<th><?php echo $this->Paginator->sort('citation_id');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($soils as $soil):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $soil['Soil']['id']; ?>&nbsp;</td>
		<td><?php echo $soil['Soil']['name']; ?>&nbsp;</td>
		<td><?php echo $soil['Soil']['thermal_diffusivity_m2_sec']; ?>&nbsp;</td>
		<td><?php echo $soil['Soil']['particle_size']; ?>&nbsp;</td>
		<td><?php echo $soil['Soil']['water_content']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($soil['Citation']['name'], array('controller' => 'citations', 'action' => 'view', $soil['Citation']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($soil['User']['name'], array('controller' => 'users', 'action' => 'view', $soil['User']['id'])); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $soil['Soil']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $soil['Soil']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $soil['Soil']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $soil['Soil']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Soil', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>