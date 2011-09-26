<div class="grid_7">
    <div class="smartbox clearfix">
        <h1 class="sbHeading">Feeling Overwhelmed?</h1>

        <p>
            Welcome to the help centre. Don't panic. Press a button instead:
        </p>

            <?php echo $this->Html->link(
                    "I'm considering destructive sampling...",
                    array ('controller' => 'pages', 'action' => 'help', 'curator_intro'),
                    array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false)); ?>

            <?php echo $this->Html->link(
                    "I'm writing with/reading your numbers...",
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