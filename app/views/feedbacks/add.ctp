<div class="feedbacks form">
<?php echo $this->Form->create('Feedback');?>
	<fieldset>
        <div class="grid_6 alpha">
            <?=$this->Form->input('title', array (
                'label' => 'Subject',
            ));?>
        </div>
        <div class="grid_6 omega">
            <?=$this->Form->input('email', array (
                'label' => 'Email address (if you would like a response)'
            ));?>
        </div>
        <div class="grid_12 alpha">
            <?=$this->Form->input('body');?>
        </div>
        <div class="grid_12 alpha">
            <?=$this->Form->submit('Send Feedback', array(
                'div' => false,
                'class' => 'fg-button ui-corner-all ui-state-default ui-priority-primary',
                'escape' => false)); ?>
        </div>
	</fieldset>
<?php echo $this->Form->end();?>
</div>
<?php
/*
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Feedbacks', true), array('action' => 'index'));?></li>
	</ul>
</div>
*/
?>