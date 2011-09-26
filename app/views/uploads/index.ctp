<div class="uploads index">
	<h2><?php __('Uploads');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('size');?></th>
			<th><?php echo $this->Paginator->sort('mime_type');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th><?php echo $this->Paginator->sort('citation_id');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('file_contents');?></th>
			<th><?php echo $this->Paginator->sort('file_location');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($uploads as $upload):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $upload['Upload']['id']; ?>&nbsp;</td>
		<td><?php echo $upload['Upload']['name']; ?>&nbsp;</td>
		<td><?php echo $upload['Upload']['title']; ?>&nbsp;</td>
		<td><?php echo $upload['Upload']['size']; ?>&nbsp;</td>
		<td><?php echo $upload['Upload']['mime_type']; ?>&nbsp;</td>
		<td><?php echo $upload['Upload']['description']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($upload['Citation']['name'], array('controller' => 'citations', 'action' => 'view', $upload['Citation']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($upload['User']['name'], array('controller' => 'users', 'action' => 'view', $upload['User']['id'])); ?>
		</td>
		<td><?php echo $upload['Upload']['file_contents']; ?>&nbsp;</td>
		<td><?php echo $upload['Upload']['file_location']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $upload['Upload']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $upload['Upload']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $upload['Upload']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $upload['Upload']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Upload', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Temporothermals', true), array('controller' => 'temporothermals', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Temporothermal', true), array('controller' => 'temporothermals', 'action' => 'add')); ?> </li>
	</ul>
</div>