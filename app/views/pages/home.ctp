
<div class="grid_7">
    <div class="smartbox">
        <h1 class="sbHeading"><div class="heading-bones">Got Bone?</div></h1>

        <div class="smartHr">&nbsp;</div>
        <div class="ui-ish clearfix">
            <?= $this->Html->image("skele_dance_small.png", array("alt" => "dancing skeletons", 'class' => 'lead-image-small')) ?>
            <p>
                If you know a little bit about the conditions a bone was buried in, how old it is and where in the world it came from then why not see how well the DNA could be preserved?
            </p>
            <?php echo $this->Html->link(
                    "Start the DNA Screening Wizard!",
                    array ('controller' => 'wiz', 'action' => 'dna_survival_screening_tool'),
                    array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
        </div>

        <div class="ui-ish clearfix">
            <div class="smartHr">&nbsp;</div>
            <?= $this->Html->image("lambda_graph_small.png", array("alt" => "graphs of lambda", 'class' => 'lead-image-small')) ?>
            <p>
                No date? If you've got qualitative DNA data for a sample and you know where it's from, then give
                our sample dating proxy tool a go!
            </p>
            <?php echo $this->Html->link(
                    "Date My Bone!",
                    array ('controller' => 'wiz', 'action' => 'age_proxy_tool'),
                    array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>

        </div>

        <div class="ui-ish clearfix ui-corner-bottom">
            <div class="smartHr">&nbsp;</div>
            <?= $this->Html->image("ice_cliffs_small.png", array("alt" => "ice cliff", 'class' => 'lead-image-small')) ?>
            <p>
                A pie left in the fridge for a week has a <em>10&deg;C thermal age</em> of less than a
                week (assuming the fridge is below 10&deg;C, which it should be!). A thermal age is only
                valid for a single chemical reaction (because they have individual responses to heat).
            </p>
            <?php echo $this->Html->link(
                    "Get a Thermal Age",
                    array ('controller' => 'wiz', 'action' => 'age_proxy_tool'),
                    array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)
            ); ?>
        </div>
    </div>
</div>



<div class="grid_5">
    <div class="smartbox">
        <h2 class="sbHeading">Welcome!</h2>
        <p>
            Thermal-age.eu is the development site for <a href="http://www.synthesys.info/II_JRA_1.htm" target="_blank">JRA 1: PrediCtoR</a> A novel decision-making software tool being developed within the framework of SYNTHESYS, the European Union-funded Integrated Activities project designed to help collections managers and users to quantify the risks associated with destructive analysis of specimens.
        </p>
        <?php echo $this->Html->link('Read more...', array ('controller' => 'pages', 'action' => 'about'), array('class' => 'fg-button ui-state-default ui-corner-all cta-button')); ?>
    </div>
    <div class="grid_under">
        <div class="smartbox">
            <h2 class="sbHeading">Quick Links</h2>

            <?php echo $this->Html->link(
                    $this->Html->image("synthlogo_274_trans.png", array("alt" => "SYNTHESYS logo")) .
                    "<br />Funding under SYNTHESYS",
                    'http://www.synthesys.info/',
                    array('escape' => false, 'target' => '_blank', 'class' => 'imagey-link ui-corner-all', 'style' => 'min-width: 257px; max-width: 274px;')
            );?>

            <?php echo $this->Html->link(
                    $this->Html->image("bioarch_200_trans.png", array("alt" => "BioArch logo")) .
                    "<br />Developed at BioArch",
                    'https://sites.google.com/a/palaeo.eu/xtnl-bioarch/Home',
                    array('escape' => false, 'target' => '_blank', 'class' => 'imagey-link ui-corner-all', 'style' => 'min-width: 257px; max-width: 274px;')
            );?>

            <div class="smartHr">&nbsp;</div>
            
            <div class="clearfix" style="padding: 20px 1em 10px 1em; text-align: center;">
                <g:plusone size="medium" href="http://thermal-age.eu/"></g:plusone>
                <a href="http://twitter.com/share" class="twitter-share-button" data-count="horizontal">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>
            </div>

        </div>
    </div>
</div>



<script type="text/javascript" src="https://apis.google.com/js/plusone.js">
  {lang: 'en-GB'}
</script>
