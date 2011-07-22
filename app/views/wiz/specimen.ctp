<?php echo $this->Form->create('Specimen', array('id' => 'SpecimenForm', 'url' => $this->here)); ?>
	<fieldset>
 		<legend><?php __('Specimen'); ?></legend>
        <div class="grid_7 alpha">
            <?= $this->Form->input('Specimen.name', array('label' => 'Name:'));?>
        </div>
        <div class="grid_4 omega">
            <?= $this->Form->input('Specimen.code', array('label' => 'Specimen #:'));?>
        </div>
        <div style="clear: both;">
            <?= $this->Form->input('Specimen.description', array('label' => 'Description:', 'rows' => 3));?>
        </div>
    </fieldset>



        <div id="wizardBottomBar" class="ui-corner-bottom ui-state-default clearfix">
            <div id="wizardProgressButtons" class="clearfix grid_8 alpha no-v-margin">
                <div class="paddedCell_5">

                    <?php echo $this->Html->link(
                        '&laquo; Previous',
                        array ('controller' => 'pages', 'action' => 'help', 'curator_intro'),
                        array('class' => 'fg-button ui-corner-left ui-state-default ui-priority-secondary', 'escape' => false)); ?>
                    <?php echo $this->Html->link(
                        'Cancel',
                        array ('controller' => '', 'action' => '', ''),
                        array('class' => 'fg-button ui-corner-right ui-state-default ui-priority-secondary', 'escape' => false)); ?>

                    
                    <?php echo $this->Form->submit('Continue &raquo;', array('div' => false, 'class' => 'fg-button ui-corner-all ui-state-default', 'escape' => false)); ?>
                    
                </div>
            </div>
            <a id="wizardProgressBar" class="clearfix grid_4 omega no-v-margin ui-corner-br"
               href="<?=$this->Html->url (array ('controller' => 'wiz', 'action' => 'progress')) ?>">
                <div class="progressbarPadding ui-state-default hover ui-corner-br">
                    <div style="padding-right: 5px; float: right; text-align: right; font-weight: bold;" class="">
                        61%
                    </div>
                    <div id="wpbContainer" class=""></div>
                </div>
            </a>
        </div>
<?php echo $this->Form->end(); ?>