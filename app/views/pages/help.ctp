<div class="grid_7">
    <div class="smartbox clearfix">
        <h1 class="sbHeading">Feeling Overwhelmed?</h1>

        <p>
            Welcome to the help centre. Don't panic. Press a button instead:
        </p>

            <?php echo $this->Html->link(
                    "I have ancient biomaterial in my museum/university/attic and am considering destructive sampling for DNA extraction",
                    array ('controller' => 'pages', 'action' => 'help', 'curator_intro'),
                    array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>

            <?php echo $this->Html->link(
                    "I am reading/writing something and want to make/retrieve/understand a reference/thermal age/&lambda; value",
                    array ('controller' => 'pages', 'action' => 'help', 'researcher_intro'),
                    array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>

            <?php echo $this->Html->link(
                    "I have no idea what's going on...",
                    array ('controller' => 'pages', 'action' => 'help', 'intro'),
                    array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>
        
        <div id="flying-donkey" class="ui-corner-br"></div>
    </div>
</div>
<div class="grid_5">
    <div class="smartbox">
        <h2 class="sbHeading">Documentation</h2>
        <p>hello world</p>
    </div>
</div>