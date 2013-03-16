
            <?php
            // I have run this experiment
            //   PCR target amplicon length & %age of samples success
            //   OR
            //   Mean fragment length of *target* genes - NOT INCLUDING CONTAMINANTS!
            //  My sample size was X milligrams
            // I intend to run this experiment
            //  I will let you know the results on (date picker), please email me a reminder
            // I do not intend to run this experiment
            //  The run was totally hypothetical/I just wanted to know about a place/time
            //  
            ?>
            <style>
                .sentenceForm {
                    font-size: x-large;
                }
                .sentenceForm select {
                    font-size: large;
                }
                
                div.sentenceForm {
                    margin: 2em;
                }
                .sentenceForm input[type="text"] {
                    width: 3em;
                    font-size: larger;
                }
                .sentenceForm span {
                    margin: 0 .2em;
                }
            </style>
            <div class="labResults form sentenceForm">
            <?php echo $this->Form->create('LabResult');?>
                <span>I</span>
                <select name="status">
                    <option>Choose an Option</option>
                    <option value="run">have</option>
                    <option value="will_run">intend to</option>
                    <option value="hypothetical">do not intend to</option>
                </select>
                <span>run this experiment.</span>
                <table style="width: 100%" data-det-field="status" data-det-val="run">
                    <thead>
                        <tr>
                            <th scope="col">
                                Experiment Type
                            </th>
                            <th scope="col" data-det-field="exp_type" data-det-val="pcr">
                                Target Length
                            </th>
                            <th scope="col" data-det-field="exp_type" data-det-val="pcr">
                                Repeats
                            </th>
                            <th scope="col" data-det-field="exp_type" data-det-val="pcr">
                                Successes
                            </th>
                            <th scope="col" data-det-field="exp_type" data-det-val="htp" colspan="3">
                                Mean fragment length <strong>not</strong> including contaminants
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="exp_type">
                                    <option>Choose an Option</option>
                                    <option value="pcr">PCR</option>
                                    <option value="htp">high throughput</option>
                                </select>
                            </td>
                            <td data-det-field="exp_type" data-det-val="pcr">
                                <input type="text" name="amplicon_length[]" />bp
                            </td>
                            <td data-det-field="exp_type" data-det-val="pcr">
                                <input type="text" name="num_runs[]" />
                            </td>
                            <td data-det-field="exp_type" data-det-val="pcr">
                                <input type="text" name="success_runs[]" />
                            </td>
                            <td data-det-field="exp_type" data-det-val="htp" colspan="3">
                                <input type="text" name="mean_fragment_length[]" />bp
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div data-det-field="status" data-det-val="will_run">
                     <span>Please remind me to supply results on</span>
                     <input type="text" class="datePicker" name="remind_me_date" />
                </div>
                <?php
                echo $this->Form->submit ("Save",array ('class' => 'fg-button ui-state-default cta-button'));
                echo $this->Form->end();
                ?>
            </div>
            <script type="text/javascript">
                (function ($) {
                    $('.sentenceForm').each (function () {
                        var $fm = $(this);
                        $fm.find('.datePicker').datepicker({
                            showAnim: 'slideDown'
                        }).css({width: '5em'});
                        $('select',this).change (function () {
                            var $sel = $(this);
                            $('[data-det-field="'+$sel.attr('name')+'"]').each (function () {
                                var $this = $(this);
                                if ($this.attr('data-det-val') == $sel.val())
                                    $this.show();
                                else
                                    $this.hide();
                            });
                        }).change();
                        $fm.find ('div,td').each (function () {
                            var $this = $(this);
                            var df = $this.attr('data-det-field');
                            if (df.length > 0) {
                                $this.hide();
                            }
                        });
                    });
                }(jQuery));
            </script>
      
<?php
/*
?>
<div class="labResults form">
<?php echo $this->Form->create('LabResult');?>
	<fieldset>
		<legend><?php __('Add Lab Result'); ?></legend>
	<?php
		echo $this->Form->input('user_id');
		echo $this->Form->input('experiment_type');
		echo $this->Form->input('htp_mfl_less_contaminants');
		echo $this->Form->input('pcr_tgt_length');
		echo $this->Form->input('pcr_num_runs');
		echo $this->Form->input('pcr_num_successes');
		echo $this->Form->input('job_id');
		echo $this->Form->input('notes');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Lab Results', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Jobs', true), array('controller' => 'jobs', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Job', true), array('controller' => 'jobs', 'action' => 'add')); ?> </li>
	</ul>
</div>
<?php
*/
?>