<div class="reactions index">
	<h2><?php __('Reactions');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('name');?></th>
			<th><?php echo $this->Paginator->sort('molecule_name');?></th>
			<th><?php echo $this->Paginator->sort('reaction_name');?></th>
			<th><?php echo $this->Paginator->sort('ea_kj_per_mol');?></th>
			<th><?php echo $this->Paginator->sort('f_sec');?></th>
			<th><?php echo $this->Paginator->sort('user_id');?></th>
			<th><?php echo $this->Paginator->sort('citation_id');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($reactions as $reaction):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $reaction['Reaction']['id']; ?>&nbsp;</td>
		<td><?php echo $reaction['Reaction']['name']; ?>&nbsp;</td>
		<td><?php echo $reaction['Reaction']['molecule_name']; ?>&nbsp;</td>
		<td><?php echo $reaction['Reaction']['reaction_name']; ?>&nbsp;</td>
		<td><?php echo $reaction['Reaction']['ea_kj_per_mol']; ?>&nbsp;</td>
		<td><?php echo $reaction['Reaction']['f_sec']; ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($reaction['User']['name'], array('controller' => 'users', 'action' => 'view', $reaction['User']['id'])); ?>
		</td>
		<td>
			<?php echo $this->Html->link($reaction['Citation']['name'], array('controller' => 'citations', 'action' => 'view', $reaction['Citation']['id'])); ?>
		</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $reaction['Reaction']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $reaction['Reaction']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $reaction['Reaction']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $reaction['Reaction']['id'])); ?>
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
		<li><?php echo $this->Html->link(__('New Reaction', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
	</ul>
</div>