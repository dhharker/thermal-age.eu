<div class="grid_12"><div class="smartbox">
    <p>
        Our technology will boil tens of thousands of years of underground temperature fluctuation down to
        a single magic number - unsurprisingly it's pretty intense behind the scenes. We have
        developed these wizards to make things simpler, quicker and easier. Please choose one to get
        started:
    </p>
</div></div>

<div class="grid_4">
    <div class="smartbox cleafix">
        <h2 class="sbHeading">
            Thermal Age
        </h2>

        <?php echo $this->Html->link(
            "Thermal Age Tool<br /><span class=\"subtler-text\">Click to Start</span>",
            array ('controller' => 'wiz', 'action' => 'age_proxy_tool'),
            array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>

        <div class="spoiler">
            <p>
                Thermal age expresses the age of a sample in "equivalent years at 10°C" for a given reaction.
                It is an absolute measure, meaning that two different bones with the same (DNA depurination)
                thermal age should have experienced the same amount of bond breakage due to depurination and will
                have the same mean fragment length and fragment length distribution.
            </p>
            <p>
                This property makes thermal age useful for comparing samples of different ages and from different
                sites, e.g. for choosing which one of two samples to spend limited funds on sampling.
            </p>
        </div>
    </div>
</div>

<div class="grid_4">
    <div class="smartbox">
        <h2 class="sbHeading">
            DNA Screener
        </h2>
        <?php echo $this->Html->link(
            "DNA Screening Wizard<br /><span class=\"subtler-text\">Click to Start</span>",
            array ('controller' => 'wiz', 'action' => 'dna_survival_screening_tool'),
            array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
       <div class="spoiler">
            <p>
                Because thermal age is the absolute amount of a reaction which has taken place in a sample, we
                can define thermal ages above which DNA is so degraded as to be not worth sampling. This varies
                with experimental design and sequencing methodology.
            </p>
        </div>
    </div>
</div>

<div class="grid_4">
    <div class="smartbox">
        <h2 class="sbHeading">
            Kinetic Dating
        </h2>
        <?php echo $this->Html->link(
            "Date Proxy Wizard<br /><span class=\"subtler-text\">Click to Start</span>",
            array ('controller' => 'wiz', 'action' => 'age_proxy_tool'),
            array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
        <div class="spoiler">
            <p>
                You can work out thermal age two ways, by estimating the temperature history, or by
                extracting the DNA and having a look at it. Knowing where in the world a bone has
                come from and a little about its burial conditions allows us to work backwards in time,
                modelling the thermal age until it matches the measured value. The point at which it
                matches is an estimate of the age of the sample.
            </p>
            <p>
                This is a new dating technique and since it is hard to quantify the error associated
                with it, it may be more helpful for testing between distinct age
                hypotheses rather than attaining a water-tight date.
            </p>
            <p>
                We believe this technique has exciting potential as a dating proxy using other
                molecules than DNA, so we'll perhaps be making announcements about this in the future.
                It's here for your entertainment until then!
            </p>
        </div>
    </div>
</div>