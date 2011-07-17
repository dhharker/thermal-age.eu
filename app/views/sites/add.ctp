<div class="sites form">
<?php echo $this->Form->create('Site');?>
	<fieldset>
 		<legend><?php __('Add Site'); ?></legend>
	<?php

Warning: Invalid argument supplied for foreach() in /usr/share/php/cake/console/templates/default/views/form.ctp on line 26
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Sites', true), array('action' => 'index'));?></li>
	</ul>
</div>