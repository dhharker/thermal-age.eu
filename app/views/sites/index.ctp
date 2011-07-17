<div class="sites index">
	<h2><?php __('Sites');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
	
Warning: Invalid argument supplied for foreach() in /usr/share/php/cake/console/templates/default/views/index.ctp on line 24
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

Warning: Invalid argument supplied for foreach() in /usr/share/php/cake/console/templates/default/views/index.ctp on line 39
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $site['Site'][''])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $site['Site'][''])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $site['Site']['']), null, sprintf(__('Are you sure you want to delete # %s?', true), $site['Site'][''])); ?>
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
	</ul>
</div>