<div class="sites view">
<h2><?php  __('Site');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>

Warning: Invalid argument supplied for foreach() in /usr/share/php/cake/console/templates/default/views/view.ctp on line 24
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Site', true), array('action' => 'edit', $site['Site'][''])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Site', true), array('action' => 'delete', $site['Site']['']), null, sprintf(__('Are you sure you want to delete # %s?', true), $site['Site'][''])); ?> </li>
		<li><?php echo $this->Html->link(__('List Sites', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Site', true), array('action' => 'add')); ?> </li>
	</ul>
</div>
