<div class="labResults index">
	<h2><?php __('Lab Results');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('updated');?></th>
			<th><?php echo $this->Paginator->sort('experiment_type');?></th>
			<th><?php echo $this->Paginator->sort('htp_mfl_less_contaminants');?></th>
			<th><?php echo $this->Paginator->sort('pcr_tgt_length');?></th>
			<th><?php echo $this->Paginator->sort('pcr_num_runs');?></th>
			<th><?php echo $this->Paginator->sort('pcr_num_successes');?></th>
			<th><?php echo $this->Paginator->sort('job_id');?></th>
			<th><?php echo $this->Paginator->sort('notes');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($labResults as $labResult):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $labResult['LabResult']['id']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($labResult['User']['name'], array('controller' => 'users', 'action' => 'view', $labResult['User']['id'])); ?>
		</td>
		<td><?php echo $labResult['LabResult']['created']; ?>&nbsp;</td>
		<td><?php echo $labResult['LabResult']['updated']; ?>&nbsp;</td>
		<td><?php echo $labResult['LabResult']['experiment_type']; ?>&nbsp;</td>
		<td><?php echo $labResult['LabResult']['htp_mfl_less_contaminants']; ?>&nbsp;</td>
		<td><?php echo $labResult['LabResult']['pcr_tgt_length']; ?>&nbsp;</td>
		<td><?php echo $labResult['LabResult']['pcr_num_runs']; ?>&nbsp;</td>
		<td><?php echo $labResult['LabResult']['pcr_num_successes']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($labResult['Job']['title'], array('controller' => 'jobs', 'action' => 'view', $labResult['Job']['id'])); ?>
		</td>
		<td><?php echo $labResult['LabResult']['notes']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $labResult['LabResult']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $labResult['LabResult']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $labResult['LabResult']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $labResult['LabResult']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Lab Result', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Jobs', true), array('controller' => 'jobs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Job', true), array('controller' => 'jobs', 'action' => 'add')); ?> </li>
	</ul>
</div>