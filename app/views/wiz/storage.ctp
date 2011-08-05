<h1 class="sbHeading ui-corner-tl">
    Storage
</h1>
<?php echo $this->Form->create('Temporothermal', array('id' => 'TemporothermalForm', 'url' => $this->here)); ?>
	<fieldset>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Temporothermal.name');?>
        </div>
        <div class="grid_11 alpha">
            <div class="grid_2 alpha">
                <?=$this->Form->input('Temporothermal.temp_mean_c', array (
                'label' => 'T<sub>mean</sub> (°C)'
            ));?>
            </div>
            <div class="grid_2">
                <?=$this->Form->input('Temporothermal.temp_pp_amp_c', array (
                'label' => 'T<sub>max</sub> ─ T<sub>min</sub> (°C)'
            ));?>
            </div>
            <!-- not yet <div class="grid_2 omega">
                <?=$this->Form->input('Temporothermal.upload_id');?>
            </div>-->
        
            <div class="grid_2">
                <?=$this->Form->input('Temporothermal.startdate_ybp', array (
                    'label' => 'Recent Date'
                ));?>
            </div>
            <div class="grid_2">
                <?=$this->Form->input('Temporothermal.range_years');?>
            </div>
            <div class="grid_2 omega">
                <?=$this->Form->input('Temporothermal.stopdate_ybp', array (
                    'label' => 'Ancient Date'
                ));?>
            </div>
        </div>
        <div class="grid_11 alpha">
            
            <?=$this->Form->input('Temporothermal.description');?>
        </div>

	</fieldset>

<?=$this->element('wiz/wizardControlBar',  array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(document).ready (function () {
    wc.initTemporothermalForm ();
});
</script>


<!--
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Temporothermal.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Temporothermal.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Temporothermals', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Uploads', true), array('controller' => 'uploads', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Upload', true), array('controller' => 'uploads', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
	</ul>
</div>
    -->