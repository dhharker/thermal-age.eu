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
                    'label' => 'Excavated (AD)',
                    'div' => 'makeInputAd',
                    'default' => ''
                ));?>
            </div>
            
            <div class="grid_3 omega">
                <?=$this->Form->input('Temporothermal.stopdate_ybp', array (
                    'label' => 'Deposited (bp)',
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
            if ($numLayers < 1)
                $numLayers = 1;
            // numlayers view logic here

            echo $this->Form->input ('numLayers', array (
                'type' => 'hidden',
                'value' => $numLayers,
            ));?>
            <div id="burialLayersList" class="smartbox grid_10 alpha" style="clear: both;">
                <div class="" style="background: url('/img/burial_surface.png') bottom center no-repeat; margin: -4px 0px 0px 0px; height: 54px;">
                    <p class="help">
                        The soil surface is up here, the sample is buried down by the &quot;Add Layer&quot; button.
                    </p>
                </div>
                <ul class="ui-sortable smartsharp">
                    <?php
                    for ($i = -1; $i < $numLayers; $i++) {
                    $n = $i;
                    /* The first layer is a hidden template for the cs js to use for the add layer
                     * button so hide it */
                        $hideLi = ($i == -1) ? ' style="display: none"' : '';

                    ?>
                    <li class="grid_10 alpha smartsharp burialLayer"<?=$hideLi?>>
                        <fieldset style="clear: both" class="">
                            <div class="lcButtons">
                                <?php echo $this->Html->link(
                                    '<span class="ui-icon ui-icon-carat-2-n-s"></span>',
                                    '#',
                                    array(
                                        'class' => 'fg-button ui-state-default sort-handle reorderLayerButton',
                                        'escape' => false,
                                    ));
                                ?>
                                <?php echo $this->Html->link(
                                    '<span class="ui-icon ui-icon-closethick"></span>',
                                    '#',
                                    array(
                                        'class' => 'fg-button ui-state-default deleteLayerButton',
                                        'escape' => false,
                                    ));
                                ?>
                            </div>
                            <div class="mobileLayers">
                                <div class="grid_3">
                                    <?=$this->Form->input('SoilTemporothermal.'.$n.'.soil_id', array (
                                        'label' => 'Layer of'
                                    ));?>
                                </div>
                                <div class="grid_3">
                                    <?=$this->Form->input('SoilTemporothermal.'.$n.'.thickness_m', array (
                                        'label' => 'Thick (m)'
                                    ));?>
                                </div>
                                <div class="grid_3 omega">
                                    <?=$this->Form->input('SoilTemporothermal.'.$n.'.sudden');?>
                                    <?=$this->Form->input('SoilTemporothermal.'.$n.'.direct_sunlight');?>
                                </div>
                                <?/*=*/$this->Form->input('SoilTemporothermal.'.$n.'.order', array (
                                    'type' => 'hidden',
                                    'class' => 'layerOrder',
                                ));?>
                                <input type="hidden" id="SoilTemporothermal<?=$n?>Order" name="data[SoilTemporothermal][<?=$n?>][order]" class="layerOrder">
                            </div>
                            

                        </fieldset>
                    </li>
                    <?php
                    }
                    ?>
                </ul>
                <div style="background: url('/img/burial_sample.png') top right no-repeat; height: 54px; margin-top: 1px;">
                <?php echo $this->Html->link(
                    '<span><span class="ui-icon ui-icon-arrowthick-1-n" style=" display: inline; top: .325em; margin-left: -3.2em; margin-top: .0em"></span>Add Layer</span>',
                    '',
                    array(
                        'class' => 'fg-button ui-state-default ui-corner-bottom cta-button',
                        'escape' => false,
                        'id' => 'addSoilLayerButton',
                        'style' => "margin: -2px 20px 10px 21px; width: 205px; text-align: center; padding-left: 1.7em; font-weight: normal; border: none; border-top: 1px solid #65735c"
                    ));
                ?>
                </div>

            </div>
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