<div class="feedbacks form">
<?php echo $this->Form->create('Feedback');?>
	<fieldset>
        <div class="grid_6">
            <?=$this->Form->input('title', array (
                'label' => 'Subject',
            ));?>
        </div>
        <div class="grid_6">
            <?=$this->Form->input('email', array (
                'label' => 'Email address (if you would like a response)'
            ));?>
        </div>
        <div class="grid_12">
            <?=$this->Form->input('body');?>
        </div>
        <div class="grid_12">
            <?=$this->Form->submit('Send Feedback', array(
                'div' => false,
                'class' => 'fg-button ui-corner-all ui-state-default ui-priority-primary',
                'escape' => false)); ?>
        </div>
	</fieldset>
    <?=$this->Form->input ('uri', array (
        'type' => 'hidden',
        'default' => 'feedback'
    ));?>
    <script type="text/javascript">
        $('input[name="uri"]').value (document.location);
    </script>

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