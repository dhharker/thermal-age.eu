<div class="layers index">
	<h2><?php __('Layers');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('sudden');?></th>
			<th><?php echo $this->Paginator->sort('soil_id');?></th>
			<th><?php echo $this->Paginator->sort('thickness_m');?></th>
			<th><?php echo $this->Paginator->sort('order');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($layers as $layer):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $layer['Layer']['id']; ?>&nbsp;</td>
		<td><?php echo $layer['Layer']['sudden']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($layer['Soil']['name'], array('controller' => 'soils', 'action' => 'view', $layer['Soil']['id'])); ?>
		</td>
		<td><?php echo $layer['Layer']['thickness_m']; ?>&nbsp;</td>
		<td><?php echo $layer['Layer']['order']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $layer['Layer']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $layer['Layer']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $layer['Layer']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $layer['Layer']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Layer', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Soils', true), array('controller' => 'soils', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Soil', true), array('controller' => 'soils', 'action' => 'add')); ?> </li>
	</ul>
</div>