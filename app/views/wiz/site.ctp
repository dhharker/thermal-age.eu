<h1 class="sbHeading ui-corner-tl">
    Site Info
</h1>
<?php echo $this->Form->create('Site', array('id' => 'SiteForm', 'url' => $this->here)); ?>
	<fieldset>
        <div class="grid_8 alpha">
            <?= $this->Form->input('Site.name'); ?>
        </div>
        <div class="grid_3 omega">
            <?= $this->Form->button ('Search Wikipedia', array (
                'id' => 'FindLatLonBySiteNameButton',
                'class' => 'fg-button ui-state-default ui-priority-primary ui-corner-all griddedButton'
            )); ?>
        </div>
        <div id="reverseGeocodeResults" class="grid_11 alpha ui-corner-all smartbox">

        </div>
        <div class="grid_4 alpha">
            <?= $this->Form->input('Site.lat_dec', array (
                'label' => 'Latitude (decimal °N)'
            )); ?>
        </div>
        <div class="grid_4">
            <?= $this->Form->input('Site.lon_dec', array (
                'label' => 'Longitude (decimal °E)'
            )); ?>
        </div>
        <div class="grid_3 omega">
            <?= $this->Form->button ('Find on Map', array (
                'id' => 'FindLatLonByMapButton',
                'class' => 'fg-button ui-state-default ui-priority-primary ui-corner-all griddedButton'
            )); ?>
        </div>
        
        <div id="gMapGridBox" class="grid_11 alpha ui-corner-all smartbox" style="overflow: hidden; clear: both; margin: 20px 0px; display: none;">
            <div id="gMapContainer" class="mapContainer"></div>
        </div>
        <div class="ui-helper-clearfix"></div>
        <div class="smallForm">
            <div class="grid_4 alpha">
                <?= $this->Form->input('Site.elevation_dem_coarse', array (
                    'label' => 'Coarse (1°, PMIP2) Elevation (m)',
                    'disabled' => 1
                )); ?>
            </div>
            <div class="grid_4">
                <?= $this->Form->input('Site.elevation_dem_fine', array (
                    'label' => 'Hi-res (0°05\',Worldclim) Elevation (m)',
                    'disabled' => 1
                )); ?>
            </div>
            <div class="grid_3 omega">
                <?= $this->Form->input('Site.coarse_fine_lapse_correction', array (
                    'label' => 'Coarse &lt; Hi-res (&deg;C&Delta;)',
                    'disabled' => 1
                )); ?>
            </div>
        </div>

        <div class="grid_4 alpha">
            <?= $this->Form->input('Site.elevation', array (
                'label' => 'Site Elevation (m, WGS84)',
                'escape' => false,
                'after' => '<small id="SiteElevationCitationText" class="inset-source-label"></small>'
            )); ?>
            <?= $this->Form->input('Site.elevation_source', array (
                'type' => 'hidden'
            )); ?>
        </div><?php /*
        <div class="grid_2">
            <?= $this->Form->input('Site.lapse_rate', array (
                'label' => 'Lapse (&deg;C/km)',
                'value' => '6.4',
                'disabled' => 1
            )); ?>
        </div>*/ ?>
        <div class="grid_4">
            <?= $this->Form->input('Site.lapse_correct', array (
                'label' => 'Hi-res &lt; Site Lapse?',
                'type' => 'checkbox',
                'value' => 1
            )); ?>
            <small>
                Correct temperature by: <br />6.4&deg;C/km × (<em>Hi-res</em> ─ <em>Site Elevation</em>)
            </small>
        </div>
        <div class="grid_3 omega">
            <?= $this->Form->input('Site.fine_known_lapse_correction', array (
                'label' => 'Hi-res &lt; Site (&deg;C&Delta;)',
                'disabled' => 1,
            )); ?>
        </div>
        <!-- pointless at this time
        <div class="grid_11 alpha">
            <?= $this->Form->input('Site.citation_id'); ?>
        </div>
        -->
        <div class="grid_11 ">
            <?= $this->Form->input('Site.description', array ('rows' => 3)); ?>
        </div>

	</fieldset>

<?=$this->element('wiz/wizardControlBar',  array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">

    wc.initSiteForm ();
</script>

<!--
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('controller' => 'sites', 'action' => 'delete', $this->Form->value('Site.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Site.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Sites', true), array('controller' => 'sites', 'action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
	</ul>
</div>-->


