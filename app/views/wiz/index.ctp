<div class="grid_12"><div class="smartbox">
    <p>
        Our technology will boil aeons of buffered temperature fluctuation down to
        a single magic number - unsurprisingly it's pretty intense behind the scenes. We have
        developed these wizards to make things simpler, quicker and easier. Please choose one to get
        started:
    </p>
</div></div>

<div class="grid_4"><div class="smartbox">
    <h2 class="sbHeading">
        Thermal Age
    </h2>
    <p>
        Thermal age expresses the age of a sample in "equivalent years at 10°C" for a given reaction.
        It is an absolute measure, meaning that two different bones with the same (DNA depurination)
        thermal age should have experienced the same amount of bond breakage to depurination and will
        have the same mean fragment length and fragment length distribution.
    </p>
    <p>
        This property makes thermal age useful for comparing samples of different ages and from different
        sites, e.g. for choosing which one of two samples to spend limited funds on sampling.
    </p>

    <?php echo $this->Html->link(
        "Thermal Age Tool",
        array ('controller' => 'wiz', 'action' => 'age_proxy_tool'),
        array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
</div></div>

<div class="grid_4"><div class="smartbox">
    <h2 class="sbHeading">
        DNA Screener
    </h2>
    <p>
        Because thermal age is an absolute amount of a reaction which has taken place in a sample, we
        can define cut-offs above which DNA is so degraded as to be not worth sampling. This varies
        with experimental design and sequencing methodology.
    </p>
    <?php echo $this->Html->link(
        "Screening Wizard",
        array ('controller' => 'wiz', 'action' => 'dna_survival_screening_tool'),
        array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
</div></div>

<div class="grid_4"><div class="smartbox">
    <h2 class="sbHeading">
        Kinetic Dating
    </h2>
    <p>
        description
    </p>
    <?php echo $this->Html->link(
        "Date Proxy Wizard",
        array ('controller' => 'wiz', 'action' => 'age_proxy_tool'),
        array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
</div></div>