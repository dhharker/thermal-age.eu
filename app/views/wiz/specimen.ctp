<h1 class="sbHeading">
    Specimen Info
</h1>

<?php echo $this->Form->create  ('Specimen', array('id' => 'SpecimenForm', 'url' => $this->here)); ?>
	<fieldset>
        <div class="grid_7 alpha">
            <?= $this->Form->input('Specimen.name', array('label' => 'Name:'));?>
        </div>
        <div class="grid_4 omega">
            <?= $this->Form->input('Specimen.code', array('label' => 'Specimen #:'));?>
        </div>
        <div class="grid_11 alpha">
            <?= $this->Form->input('Specimen.description', array('label' => 'Description:', 'rows' => 3));?>
        </div>
    </fieldset>

<?

echo $this->element('wiz/wizardControlBar', $wizard);?>

<?php echo $this->Form->end(); ?>