<div class="feedbacks form" id="fbAjCont">
<?php echo $this->Form->create('Feedback');?>
	<fieldset>
        <div class="">
            <?=$this->Form->input('title', array (
                'label' => 'Subject',
            ));?>
        </div>
        <div class="">
            <?=$this->Form->input('email', array (
                'label' => 'Email address (if you would like a response)'
            ));?>
        </div>
        <div class="">
            <?=$this->Form->input('body');?>
        </div>
        <div class="">
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
        $(document).ready (function () {
            $('#FeedbackUri').val (document.location);
            var fbf = $('#fbAjCont');
            $('form#FeedbackAddForm').not('.axfInited').ajaxForm ({
                beforeSubmit: function () {
                        return true;
                },
                complete: function (a, b) {
                    initialiseTAUI (fbf);
                },
                target: fbf
            }).submit (function () {
                return false;
            }).addClass ('axfInited');
        });

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