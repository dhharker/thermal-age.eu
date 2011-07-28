<?=$this->element('wiz/wizardDetailColumn', $wizard);?>

<h1 class="sbHeading ui-corner-tl">
    Chemical Reaction
</h1>
<?php echo $this->Form->create('Reaction', array('id' => 'ReactionForm', 'url' => $this->here)); ?>
	<fieldset>
		

        <div class="grid_11 alpha">
            <?= $this->Form->input('Reaction.reaction_id', array (
                'style' => 'width: 100%',
                'id' => 'ReactionSelect',
            )); ?>
        </div>

        <fieldset id="ReactionDetails" style="display: none;">
            
            <div class="grid_11 alpha">
                <div class="grid_4 alpha">
                    <?= $this->Form->input('Reaction.molecule_name', array (
                        'label' => 'Molecule',
                    )); ?>
                </div>
                <div class="grid_4">
                    <?= $this->Form->input('Reaction.reaction_name', array (
                        'label' => 'Reaction'
                    )); ?>
                </div>
                <div class="grid_3 omega">
                    <?= $this->Form->input('Reaction.substrate_name', array (
                        'label' => 'Substrate'
                    )); ?>
                </div>
                <div class="grid_11 alpha">
                    <?= $this->Form->input('Reaction.name', array (
                        'label' => 'Name',
                        
                    )); ?>
                </div>
            </div>
            <div class="grid_11 alpha">
                <div class="grid_5 alpha">
                    <?= $this->Form->input('Reaction.ea_kj_per_mol', array (
                        'label' => 'Energy of Activation (kJ·mol<sup>-1</sup>)'
                    )); ?>
                </div>
                <div class="grid_6 omega">
                    <?= $this->Form->input('Reaction.f_sec', array (
                        'label' => 'Pre-exponential Factor (sec.)'
                    )); ?>
                    </div>
            </div>


            <div class="grid_11 alpha">
                <?= $this->Form->input('Reaction.citation_id', array (
                    'style' => 'width: 100%',

                )); ?>
            </div>
        </fieldset>
	</fieldset>

<?

echo $this->element('wiz/wizardControlBar', $wizard);?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(document).ready (function () {
    wc.initReactionForm ();
});
</script>






<!--
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Reaction.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Reaction.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Reactions', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Users', true), array('controller' => 'users', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New User', true), array('controller' => 'users', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Citations', true), array('controller' => 'citations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Citation', true), array('controller' => 'citations', 'action' => 'add')); ?> </li>
	</ul>
</div>-->