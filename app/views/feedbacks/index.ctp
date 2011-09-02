<div class="feedbacks index">
	<h2><?php __('Feedbacks');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('title');?></th>
			<th><?php echo $this->Paginator->sort('uri');?></th>
			<th><?php echo $this->Paginator->sort('body');?></th>
			<th><?php echo $this->Paginator->sort('client_info');?></th>
			<th><?php echo $this->Paginator->sort('email');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($feedbacks as $feedback):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $feedback['Feedback']['id']; ?>&nbsp;</td>
		<td><?php echo $feedback['Feedback']['title']; ?>&nbsp;</td>
		<td><?php echo $feedback['Feedback']['uri']; ?>&nbsp;</td>
		<td><?php echo $feedback['Feedback']['body']; ?>&nbsp;</td>
		<td><?php echo $feedback['Feedback']['client_info']; ?>&nbsp;</td>
		<td><?php echo $feedback['Feedback']['email']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $feedback['Feedback']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $feedback['Feedback']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $feedback['Feedback']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $feedback['Feedback']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Feedback', true), array('action' => 'add')); ?></li>
	</ul>
</div>