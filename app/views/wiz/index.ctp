<div class="grid_12"><div class="smartbox">
    <h1 class="sbHeading">Thermal Age Tools</h1>
    <p>
        Our technology will boil tens of thousands of years of underground temperature fluctuation down to
        a single magic number - unsurprisingly it's pretty intense behind the scenes. We have
        developed these wizards to make things simpler, quicker and easier. Please choose one to get
        started:
    </p>
</div></div>

<?php
$isRunning = '<div class="message" style="padding: .25em .2em .05em .2em; margin: .3em -5px;"><span>&ensp;'.$this->Icons->i('&#xe064;').'&ensp;In Progress</span></div>';
$curWiz = $this->Session->read('wizards.currently');
?>

<div class="grid_6">
    <div class="smartbox">
        <h2 class="sbHeading">
            DNA Screener
        </h2>
        <?php
        $wn = 'dna_survival_screening_tool';
        $verb = 'Start';
        if ($curWiz == $wn) {
            echo $isRunning;
            $verb = 'Resume';
        }
        echo $this->Html->link(
            $this->Icons->i('&#xe009;') . "&ensp; DNA Screening Wizard<br /><span class=\"subtler-text\">Click to $verb</span>",
            array ('controller' => 'wiz', 'action' => $wn),
            array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false));
        ?>
       <div class="xspoiler">
            <p>
                Because thermal age is the absolute amount of a reaction which has taken place in a sample, we
                can define thermal ages above which DNA is so degraded as to be not worth sampling. This varies
                with experimental design and sequencing methodology.
            </p>
        </div>
    </div>
</div>
<div class="grid_6">

    <div class="smartbox cleafix">

        <h2 class="sbHeading">
            Spreadsheet Wizard
        </h2>
        <?php
        $wn = 'thermal_age_spreadsheet_tool';
        $verb = 'Start';
        if ($curWiz == $wn) {
            echo $isRunning;
            $verb = 'Resume';
        }
        echo $this->Html->link(
            $this->Icons->i('&#xe04b;') . "&ensp; DNA Screener Spreadsheet<br /><span class=\"subtler-text\">Click to $verb</span>",
            array ('controller' => 'wiz', 'action' => $wn),
            array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
        <div class="xspoiler">
            <p>
                This tool supports nearly all the functions available in the DNA Screener wizard.
                Step-by-step guidance will help you configure a blank spreadsheet with the
                requisite column headings already in place and example rows to help you get started.
            </p>
        </div>

    </div>
    
</div>

<div class="grid_4">
    
</div>