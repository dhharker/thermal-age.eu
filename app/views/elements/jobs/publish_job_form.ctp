<?php
if (!isset ($editMode)) $editMode = false;
?>
<div id="PublishJobForm" class="labResults form sentenceForm cakeInline smxartbox" style="max-width: 650px; margin: 0 auto; padding: 1.5em 2em .25em 2em;">
    <?php
    if ($isAjax) {
        echo $this->Session->flash();
    }
    ?>
<?php echo $this->Form->create('Job',array (
    'url' => array (
        'action' => 'edit',
        $job['Job']['id']
    )
));?>
    <?=$this->Form->error('Job.id');?>
    <?=$this->Form->input('Job.id', array (
        'type' => 'hidden',
        'value' => $job['Job']['id']
    ));?>
    <div style="margin-bottom: 1em;">
        <span>Publish these results for public access: </span>
        <?=$this->Form->input('Job.published', array(
            'type' => 'checkbox',
            'checked' => (!!$job['Job']['published']) ? true : false,
            'div' => false,
            'style' => 'margin: .2em; float: none; display: inline; width: auto;',
            'label' => false,
        ));
        ?>
    </div>
    <div data-det-field="data[Job][published]" data-det-val="1">
        <span><?=$this->Icons->i('&#xe01d;');?> Publish any attached experimental results? </span>
        <?=$this->Form->input('Job.publish_lab_results', array(
            'type' => 'checkbox',
            'checked' => true,
            'div' => false,
            'style' => 'margin: .2em; float: none; display: inline; width: auto;',
            'label' => false,
        ));
        ?>
        <br />
        <span><?=$this->Icons->i('&#xe002;');?> Embargo the results until: </span>
        <?
        echo $this->Form->input ('Job.published_date',array (
            'type' => 'text',
            'div' => false,
            'label' => false,
            'class' => 'datePicker',
            'default' => date('Y-m-d'),
            'value' => date('Y-m-d', !empty ($job['Job']['published_date']) ? strtotime($job['Job']['published_date']) : time()+(60*60*24)),
        ));
        ?>
    </div>

    <?php
         echo $this->Form->submit ("Save",array ('class' => 'fg-button ui-state-default cta-button', 'style'=>'display: block; margin-left: auto; margin-right: auto;', 'div' => array ('style'=>'width: 100%; display: block;')));
    echo $this->Form->end();
    ?>
</div>
<script type="text/javascript">
    (function ($) {
        $(document).ready (function () {
            $('#PublishJobForm.sentenceForm').each (function () {
                var $fm = $(this);
                $fm.find('.datePicker').datepicker({
                    showAnim: 'slideDown',
                    dateFormat: 'yy-mm-dd'
                }).css({width: '6em'});
                $fm.find ('div').each (function () {
                    var $this = $(this);
                    var df = $this.attr('data-det-field');
                    if (!!df && df.length > 0) {
                        $this.hide();
                    }
                });
                var sel = $('select, :checkbox',this);
                sel.each (function () {
                    $(this).change (function () {
                        var $sel = $(this);
                        var val = ($sel.is(':checkbox')) ? (($sel.is(':checked') ? 1 : 0)) : $sel.val();
                        //console.log ($sel.is(':checkbox'), $sel.is(':checked'), $sel.val(), val);
                        var dfs = $('[data-det-field="'+$sel.attr('name')+'"]');

                        dfs.filter('[data-det-val="'+val+'"]:hidden').slideDown('slow');
                        dfs.filter('[data-det-val!="'+val+'"]:visible').slideUp('slow');

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

            var $fm = $('#PublishJobForm form');
            $fm.ajaxForm ({
                type: 'post',
                target: $('#PublishJobForm').parent()
            });
            $('#PublishJobForm a.fg-button').click(function(e) {
                var $this = $(this);
                var r = null;
                if (!!$this.attr('onload')) {
                    eval ('r = ('+$this.attr('onload')+') ? true : false;');
                }
                e.preventDefault();
                if (r === false) return false;
                $('#PublishJobForm').load($(this).attr('href'));
            });
        });

    }(jQuery));
</script>