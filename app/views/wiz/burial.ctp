<h1 class="sbHeading ui-corner-tl">
    Burial
</h1>
<?php echo $this->Form->create('Burial', array('id' => 'BurialForm', 'url' => $this->here)); ?>
	<fieldset>
        <!-- burials need not be named <div class="grid_11 alpha">
            <?=$this->Form->input('.name');?>
        </div> -->
        <div class="grid_11 alpha">
            <div class="grid_5 alpha">
                <p>
                    You will be able to define the sample storage temperature since excavation in the next screen.
                </p>
            </div>
            <div class="grid_3">
                <?=$this->Form->input('Temporothermal.startdate_ybp', array (
                    'label' => 'Excavated (AD)'
                ));?>
            </div>
            
            <div class="grid_3 omega">
                <?=$this->Form->input('Temporothermal.stopdate_ybp', array (
                    'label' => 'Deposited (b.p.)',
                    'default' => $agebp,
                    'disabled' => 'disabled'
                ));?>
            </div>

        </div>
        <div class="grid_12 alpha smartHr"></div>
        <div class="grid_11 alpha">
            <div class="spoiler">
                <p>
                    The sediments overlaying a specimen will reduce the magnitude of seasonal temperature
                    variation depending on the type of soil, moisture content and thickness of the layer.
                </p>
                <p>
                    The simple model below lets you describe the layers the sample was found under.
                    The model assumes that the layers build up consecutively and at a constant speed.
                    The &quot;Sudden&quot; checkbox makes the layer "appear all at once" and is used
                    to model landslides or other sudden burials.
                </p>
            </div>
            <?php
            $numLayers = $this->Form->value ('numLayers');
            
            // numlayers view logic here

            echo $this->Form->input ('numLayers', array (
                'type' => 'hidden',
                'value' => $numLayers,
            ));?>
            <ul id="burialLayersList">
                <?php
                for ($i = 0; $i < $numLayers; $i++) {
                ?>
                <li>
                    <fieldset style="clear: both">
                        <div class="grid_3 alpha">
                            <?=$this->Form->input('SoilTemporothermal.'.$i.'.soil_id');?>
                        </div>
                        <div class="grid_3">
                            <?=$this->Form->input('SoilTemporothermal.'.$i.'.thickness_m', array (
                                'label' => 'Thickness (m)'
                            ));?>
                        </div>
                        <div class="grid_3 omega">
                            <?=$this->Form->input('SoilTemporothermal.'.$i.'.sudden');?>
                        </div>
                      
                    </fieldset>
                </li>
                <?php
                }
                ?>
            </ul>
            
        </div>
        <div class="grid_12 alpha smartHr"></div>
        <div class="grid_11 alpha" style="display: none">

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
        </div>
        <div class="grid_11 alpha">
            
            <?=$this->Form->input('Temporothermal.description', array ('rows' => 3));?>
        </div>

	</fieldset>

<?=$this->element('wiz/wizardControlBar',  array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(document).ready (function () {
    wc.initBurialForm ();
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