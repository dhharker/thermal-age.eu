<div class="layers view">
<h2><?php  __('Layer');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $layer['Layer']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Sudden'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $layer['Layer']['sudden']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Soil'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $this->Html->link($layer['Soil']['name'], array('controller' => 'soils', 'action' => 'view', $layer['Soil']['id'])); ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Thickness M'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $layer['Layer']['thickness_m']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Order'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $layer['Layer']['order']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Layer', true), array('action' => 'edit', $layer['Layer']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Layer', true), array('action' => 'delete', $layer['Layer']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $layer['Layer']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Layers', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Layer', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Soils', true), array('controller' => 'soils', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Soil', true), array('controller' => 'soils', 'action' => 'add')); ?> </li>
	</ul>
</div>
