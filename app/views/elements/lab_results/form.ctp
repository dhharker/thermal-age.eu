<div id="LabResultJobFormTarget">
            <?php
            /*
             * Logic:
                I have run this experiment
                  PCR target amplicon length & %age of samples success
                  OR
                  Mean fragment length of *target* genes - NOT INCLUDING CONTAMINANTS!
                 My sample size was X milligrams
                I intend to run this experiment
                 I will let you know the results on (date picker), please email me a reminder
                I do not intend to run this experiment
                 The run was totally hypothetical/I just wanted to know about a place/time
             * 
             * Expects:
             *  $job_id                 Job to attach results to
             * Optional:
             *  $othersResultsTypes     e.g. array ('will_run','run') - what types of results are already recorded determines which types can be added
             *  $lockTypes              bool whether to lock the result_type and experiment_type fields (for when editing - prevents people editing their way around the restrictions of what types scan be added when other types are already present)
             *  $editMode               bool whether to include a hidden field for the LabResult id
             */
            if (!isset ($othersResultsTypes)) $othersResultsTypes = array ();
            if (!isset ($lockTypes)) $lockTypes = false;
            if (!isset ($editMode)) $editMode = false;
            ?>
            <div class="labResults form sentenceForm cakeInline smxartbox" style="max-width: 650px; margin: 0 auto; padding: 1.5em 2em .25em 2em;">
                <?php
                //$a = new FormHelper;
                //$a->cre
                if ($isAjax) {
                    echo $this->Session->flash();
                }
                ?>
            <?php echo $this->Form->create('LabResult',array (
                'url' => $this->here
            ));?>
                <?=$this->Form->error('job_id');?>
                <? if (!!$editMode)
                    echo $this->Form->input('id', array (
                        'type' => 'hidden'
                    ));?>
                <?=$this->Form->input('job_id', array (
                    'type' => 'hidden',
                    'value' => $job_id
                ));?>
                <?=$this->Form->input('after_success', array (
                    'type' => 'hidden',
                    'value' => (isset ($after_success)) ? $after_success : 'index'
                ));?>
                <span>I</span>
                <?php
                    $rtOpts = array ();
                    if (!in_array ('wont_run', $othersResultsTypes) &&
                        !in_array ('hypothetical', $othersResultsTypes)) {
                        $rtOpts['run'] = 'have';
                        $rtOpts['will_run'] = 'intend to';
                    }
                    if (!in_array ('will_run', $othersResultsTypes) &&
                        !in_array ('hypothetical', $othersResultsTypes) &&
                        !in_array ('run', $othersResultsTypes)) {
                        $rtOpts['wont_run'] = 'do not intend to';
                        $rtOpts['hypothetical'] = 'cannot (it\'s hypothetical)';
                    }
                    

                    echo $this->Form->input('result_type', array (
                        'type' => 'select',
                        'empty' => 'Choose an Option',
                        'options' => $rtOpts,
                        'div' => false,
                        'label' => false,
                        'disabled' => $lockTypes
                    ));
                ?>
                <span>run this</span>
                <?php
                    echo $this->Form->input('experiment_type', array (
                        'type' => 'select',
                        'empty' => 'Choose an Option',
                        'options' => array (
                            'pcr' => 'PCR',
                            'htp' => 'high throughput'
                        ),
                        'div' => false,
                        'label' => false,
                        'data-det-field' => 'data[LabResult][result_type]',
                        'data-det-val' => 'run',
                        'disabled' => $lockTypes
                    ));
                ?>
                <span>experiment.</span>
                <div style="width: 100%; margin-top: 1em;" data-det-field="data[LabResult][result_type]" data-det-val="run">
                    <div data-det-field="data[LabResult][experiment_type]" data-det-val="htp">
                        <?php
                            $fmt = array ("div" => array ("class"=>"input required"));
                            $cf = "<div class=\"ui-helper-clearfix\"></div>";
                            echo $this->Form->input('htp_mfl_less_contaminants', array_merge_recursive ($fmt, array ('style'=>'margin-top: .6em;', 'label' =>'Mean Fragment Length<br /><small>(<em>excluding</em> contaminants!)</small>','escape'=> false)));
                            echo $cf;
                        ?>
                    </div>
                    <div data-det-field="data[LabResult][experiment_type]" data-det-val="pcr">
                        <?php
                            echo $this->Form->input('pcr_tgt_length',    array_merge_recursive ($fmt, array ('label' =>'Target Amplicon Length')));
                            echo $cf;
                            echo $this->Form->input('pcr_num_runs',      array_merge_recursive ($fmt, array ('label' =>'Number of Runs')));
                            echo $cf;
                            echo $this->Form->input('pcr_num_successes', array_merge_recursive ($fmt, array ('label' =>'Successful Runs')));
                            echo $cf;
                        ?>
                    </div>
                    <?php
                    
                        echo $this->Form->input('labs_ref',array ('style'=>'margin-top: .6em; width: 9em;', 'label' => "Experiment Number(s)<br /><small>For your reference. Optional.</small>",'escape'=>false));
                        
                    ?>
                </div>
                    
                
                        
                <div data-det-field="data[LabResult][result_type]" data-det-val="will_run">
                     <span><?=$this->Icons->i('&#xe002;');?> Please remind me to supply results on</span>
                     <?=$this->Form->input ('remind_me',array (
                         'type' => 'text',
                         'div' => false,
                         'label' => false,
                         'class' => 'datePicker'
                     ));?>
                </div>
                <?php
                echo $this->Form->submit ("Save",array ('class' => 'fg-button ui-state-default cta-button', 'style'=>'display: block; margin-left: auto; margin-right: auto;', 'div' => array ('style'=>'width: 100%; display: block;')));
                echo $this->Form->end();
                ?>
            </div>
            <script type="text/javascript">
                (function ($) {
                    $(document).ready (function () {
                        $('.sentenceForm').each (function () {
                            var $fm = $(this);
                            $fm.find('.datePicker').datepicker({
                                showAnim: 'slideDown',
                                dateFormat: 'yy-mm-dd'
                            }).css({width: '5em'});
                            $fm.find ('div').each (function () {
                                var $this = $(this);
                                var df = $this.attr('data-det-field');
                                if (!!df && df.length > 0) {
                                    $this.hide();
                                }
                            });
                            var sel = $('select',this);
                            sel.each (function () {
                                $(this).change (function () {
                                    var $sel = $(this);
                                    var dfs = $('[data-det-field="'+$sel.attr('name')+'"]');
                                    
                                    dfs.filter('[data-det-val="'+$sel.val()+'"]:hidden').slideDown('slow');
                                    dfs.filter('[data-det-val!="'+$sel.val()+'"]:visible').slideUp('slow');
                                    
                                    /*
                                    $('[data-det-field="'+$sel.attr('name')+'"]')
                                        var $this = $(this);
                                        if ($this.attr('data-det-val') == $sel.val())
                                            $this.filter(':hidden').fadeIn('fast');
                                        else
                                            $this.filter(':visible').fadeOut('fast');
                                    });
                                    */
                                }).change();
                            });
                            
                        });

                        var $fm = $('.labResults form');
                        $fm.ajaxForm ({
                            type: 'post',
                            target: $('#LabResultsScope')
                        });
                        $('#LabResultsScope a.fg-button').click(function(e) {
                            var $this = $(this);
                            var r = null;
                            if (!!$this.attr('onload')) {
                                eval ('r = ('+$this.attr('onload')+') ? true : false;');
                            }
                            e.preventDefault();
                            if (r === false) return false;
                            $('#LabResultsScope').load($(this).attr('href'));
                        });
                    });
                    
                }(jQuery));
            </script>
      
<?php
/*
 * 

<table style="width: 100%" data-det-field="data[LabResult][result_type]" data-det-val="run">
                    <thead>
                        <tr>
                            <th scope="col" class="input text required">
                                Experiment Type
                            </th>
                            <th scope="col" data-det-field="data[LabResult][result_type]" data-det-val="pcr" class="input required">
                                Target Length
                            </th>
                            <th scope="col" data-det-field="data[LabResult][result_type]" data-det-val="pcr" class="input required">
                                Repeats
                            </th>
                            <th scope="col" data-det-field="data[LabResult][result_type]" data-det-val="pcr" class="input required">
                                Successes
                            </th>
                            <th scope="col" data-det-field="data[LabResult][result_type]" data-det-val="htp" colspan="3" class="input required">
                                Mean fragment length <strong>not</strong> including contaminants
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <select name="data[LabResult][result_type]">
                                    <option>Choose an Option</option>
                                    <option value="pcr">PCR</option>
                                    <option value="htp">high throughput</option>
                                </select>
                            </td>
                            <td data-det-field="data[LabResult][result_type]" data-det-val="pcr">
                                <input type="text" name="amplicon_length[]" />bp
                            </td>
                            <td data-det-field="data[LabResult][result_type]" data-det-val="pcr">
                                <input type="text" name="num_runs[]" />
                            </td>
                            <td data-det-field="data[LabResult][result_type]" data-det-val="pcr">
                                <input type="text" name="success_runs[]" />
                            </td>
                            <td data-det-field="data[LabResult][result_type]" data-det-val="htp" colspan="3">
                                <input type="text" name="mean_fragment_length[]" />bp
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <?=$this->Form->input('labs_ref');?>
                                <small>For your reference. Optional.</small>
                            </td>
                        </tr>
                    </tbody>
                </table>


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
</div>