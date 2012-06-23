<h1 class="sbHeading ui-corner-tl">
    Storage
</h1>
<?php echo $this->Form->create('Storage', array('id' => 'StorageForm', 'url' => $this->here)); ?>
	<fieldset>
        <!-- burials need not be named <div class="grid_11 alpha">
            <?=$this->Form->input('.name');?>
        </div> -->
        <div class="grid_5 alpha">
            <p>
                Enter the temperature of storage, and optionally how much this temperature varies
                throughout the year.
            </p>
        </div>
        <div class="grid_6 omega">
            
            <div class="grid_3 alpha">
                <?=$this->Form->input('Temporothermal.startdate_ybp', array (
                    'label' => 'Analysed (AD)',
                    'default' => date ('Y'),
                    'div' => 'makeInputAd'
                ));?>
            </div>
            <div class="grid_3 omega">
                <?=$this->Form->input('Temporothermal.stopdate_ybp', array (
                    'label' => 'Excavated (AD)',
                    'default' => $excavatedbp,
                    'disabled' => 'disabled',
                    'div' => 'makeInputAd'
                ));?>
            </div>
            <div style="clear: both"></div>
            <div class="grid_3 alpha">
                <?=$this->Form->input('Temporothermal.temp_mean_c', array (
                    'label' => 'T<sub>mean</sub> (°C)',
                    'div' => 'required'
                ));?>
            </div>
            <div class="grid_3 omega">
                <?=$this->Form->input('Temporothermal.temp_pp_amp_c', array (
                    'label' => 'T<sub>max</sub> ─ T<sub>min</sub> (°C)'
                ));?>
            </div>
            
            <div class="grid_6 alpha">
                <div class="smartbox" style="width: 280px; margin: 0px auto; padding: 6px 6px 2px 6px;">
                     <?=$this->Html->image("temp_expl_graph_small.png", array(
                         "alt" => "Graph showing temperatures",
                     ));?>
                 </div>
            </div>
        </div>


        
        <div class="grid_11 alpha" style="">

            <!-- not yet <div class="grid_2 omega">
                <?=$this->Form->input('Temporothermal.upload_id');?>
            </div>-->
        </div>
        <div class="grid_11 alpha">

            <?=$this->Form->input('Temporothermal.description', array ('rows' => 3));?>
        </div>

	</fieldset>

<?=$this->element('wiz/wizardControlBar',  array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(document).ready (function () {
    wc.initStorageForm ();
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