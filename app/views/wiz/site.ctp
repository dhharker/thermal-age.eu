<h1 class="sbHeading ui-corner-tl">
    Site Info
</h1>
<?php echo $this->Form->create('Site', array('id' => 'SiteForm', 'url' => $this->here)); ?>
	<fieldset>
        <div class="grid_5 alpha">
            <?= $this->Form->input('Site.lat_dec'); ?>
        </div>
        <div class="grid_6 omega">
            <?= $this->Form->input('Site.lon_dec'); ?>
        </div>

        <div class="grid_11 alpha ui-corner-all" style="overflow: hidden">
            <div id="gMapContainer" class="smartbox" style="height: 300px; margin: 0px 0px 0px 5px;"></div>
        </div>
        <div class="grid_11 alpha">
            <?= $this->Form->input('Site.name'); ?>
        </div>

        <div class="grid_11 alpha">
            <?= $this->Form->input('Site.citation_id'); ?>
        </div>
        <div class="grid_11 alpha">
            <?= $this->Form->input('Site.description'); ?>
        </div>

	</fieldset>

<?

echo $this->element('wiz/wizardControlBar', $wizard);?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(document).ready (function () {
    wc.initMap ('#gMapContainer');
});
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