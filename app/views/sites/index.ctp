<div class="sites index">
	<h2><?php __('Sites');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('lat_dec');?></th>
			<th><?php echo $this->Paginator->sort('lon_dec');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('citation_id');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($sites as $site):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $site['Site']['id']; ?>&nbsp;</td>
		<td><?php echo $site['Site']['name']; ?>&nbsp;</td>
		<td><?php echo $site['Site']['lat_dec']; ?>&nbsp;</td>
		<td><?php echo $site['Site']['lon_dec']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($site['User']['name'], array('controller' => 'users', 'action' => 'view', $site['User']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($site['Citation']['name'], array('controller' => 'citations', 'action' => 'view', $site['Citation']['id'])); ?>
		</td>
		<td><?php echo $site['Site']['description']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $site['Site']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $site['Site']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $site['Site']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $site['Site']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Site', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
	</ul>
</div>