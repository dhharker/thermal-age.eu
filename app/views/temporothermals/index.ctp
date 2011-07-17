<div class="temporothermals index">
	<h2><?php __('Temporothermals');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('temp_mean_c');?></th>
			<th><?php echo $this->Paginator->sort('temp_pp_amp_c');?></th>
			<th><?php echo $this->Paginator->sort('upload_id');?></th>
			<th><?php echo $this->Paginator->sort('startdate_ybp');?></th>
			<th><?php echo $this->Paginator->sort('stopdate_ybp');?></th>
			<th><?php echo $this->Paginator->sort('range_years');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('description');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($temporothermals as $temporothermal):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $temporothermal['Temporothermal']['id']; ?>&nbsp;</td>
		<td><?php echo $temporothermal['Temporothermal']['name']; ?>&nbsp;</td>
		<td><?php echo $temporothermal['Temporothermal']['temp_mean_c']; ?>&nbsp;</td>
		<td><?php echo $temporothermal['Temporothermal']['temp_pp_amp_c']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($temporothermal['Upload']['name'], array('controller' => 'uploads', 'action' => 'view', $temporothermal['Upload']['id'])); ?>
		</td>
		<td><?php echo $temporothermal['Temporothermal']['startdate_ybp']; ?>&nbsp;</td>
		<td><?php echo $temporothermal['Temporothermal']['stopdate_ybp']; ?>&nbsp;</td>
		<td><?php echo $temporothermal['Temporothermal']['range_years']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($temporothermal['User']['name'], array('controller' => 'users', 'action' => 'view', $temporothermal['User']['id'])); ?>
		</td>
		<td><?php echo $temporothermal['Temporothermal']['description']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $temporothermal['Temporothermal']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $temporothermal['Temporothermal']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $temporothermal['Temporothermal']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $temporothermal['Temporothermal']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Temporothermal', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Uploads', true), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload', true), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>