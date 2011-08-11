<!DOCTYPE HTML>

<html>

<head>
	<?php echo $this->Html->charset(); ?>
	<title>
        <?php echo $title_for_layout; ?> ::
        <?php __('thermal-age.eu'); ?>
	</title>
	<?php
		echo $this->Html->meta('icon') . "\n";

        //echo $this->Html->css('cake.generic.css') . "\n";

		echo $this->Html->css('adapt/reset.css') . "\n";


        echo $this->Html->css('adapt/text.css') . "\n";
        echo $this->Html->css('taeu-jqui-theme/jquery-ui-1.8.14.custom.css') . "\n";
        echo $this->Html->css('thermal-age.css') . "\n";

        if (isset ($isWizard) && $isWizard == TRUE) {
            echo $this->Html->css('thermal-age-wizard.css') . "\n";
        }

        // this is only the default for when js is disabled, sizes otherwise in config.js
        $cssSize = (isset ($isMobile) && $isMobile) ? 'mobile' : '960';
            
    ?>


    <noscript>
        <?= $this->Html->css('adapt/' . $cssSize . '.min.css') . "\n"; ?>
    </noscript>
            
        

    <?php
        echo $this->Minify->js_link($global_minified_javascript) . "\n";
        if (isset ($minified_javascript))
            echo $this->Minify->js_link($minified_javascript) . "\n";

		echo $scripts_for_layout . "\n";

        //when debugging: (prod add to minify)
        //$this->addScript($this->Javascript->link('jqf/jquery.form.js'));


        // @todo move this to $minified_javascript via wizController::_initialiseWizardEnvironment()
        if (isset ($isWizard) && $isWizard == TRUE) {
            echo $this->Javascript->link('wizard_components.js');
        }

        echo $this->Javascript->link('ui.js');

        // looks like this might have to be loaded last of all to make the callbacks work nicely
        //echo $this->Javascript->link('adapt/adapt.js');



	?>
</head>

<body>
    <div id="bg1"><div id="bg2">
    <div id="container" class="container_12 smartbox">

        <header class="grid_12" style="clear: both;">
            <div class="grid_4 alpha clearfix no-v-margin">
                <?php echo $this->Html->link('', '/', array('id' => 'thermalAgeLogo', 'title' => 'thermal-age.eu home')); ?>
            </div>
            <nav>
                <div class="grid_8 omega fg-buttonset fg-buttonset-single clearfix no-v-margin">
                    <div id="topMainMenu">
                        <?php $this->Html->link('Enter', array ('controller' => '', 'action' => ''), array('class' => 'button'/*, 'target' => '_blank'*/)); ?>
                        
                        <?php echo $this->Html->link('Home', '/', array('class' => 'fg-button ui-state-default  ui-corner-left')); ?>

                        <?php echo $this->Html->link('Clear', array ('controller' => 'wiz', 'action' => 'clearcache'), array('class' => 'fg-button ui-state-default')); ?>

                        <?php echo $this->Html->link('About', array ('controller' => 'pages', 'action' => 'about'), array('class' => 'fg-button ui-state-default ')); ?>

                        <?php echo $this->Html->link('Wizards', array ('controller' => 'wiz', 'action' => 'index'), array('class' => 'fg-button ui-state-default')); ?>

                        <?php  $this->Html->link('Developers', array ('controller' => 'pages', 'action' => ''), array('class' => 'fg-button ui-state-default ')); ?>

                        <?php  $this->Html->link('Data', array ('controller' => 'pages', 'action' => ''), array('class' => 'fg-button ui-state-default ')); ?>

                        <?php  $this->Html->link('Help', array ('controller' => 'pages', 'action' => 'help'), array('class' => 'fg-button ui-state-default ')); ?>

                        <?php  $this->Html->link('Login', array ('controller' => 'users', 'action' => 'login'), array('class' => 'fg-button ui-state-default  ui-corner-right')); ?>

                    </div>
                </div>
            </nav>
        </header>

        <div class="grid_12">
            <?php echo $this->Session->flash(); ?>
        </div>
        <div id="pageContent" style="clear: both;">
            <? if (isset ($isWizard) && $isWizard == TRUE) { ?>
            <div class="grid_12" id="wizardContainer">
                <div id="wizardAjaxTarget" class="smartbox clearfix ui-ish">
                    <noscript>
                        <div>
                            <p class="error ui-corner-all">
                                You need javascript enabled to use this tool.
                            </p>
                        </div>
                    </noscript>
                    <?= $content_for_layout ?>
                </div>
            </div>
            <script type="text/javascript">
            ($(document).ready(function () {
                wc.init();
            }));
            </script>
            <?php } else { ?>
            <?= $content_for_layout ?>
            <?php } ?>
        </div>

        <footer class="grid_12">
            <div class="smartbox">
                <div class="grid_6 alpha clearfix no-v-margin">
                    <div class="paddedCell_10_h">
                        &copy; Copyright 2009&ndash;<?= date ('Y') ?>
                    </div>

                </div>
                <div class="grid_6 omega clearfix no-v-margin">
                    <?php
                        echo $this->Html->link('T&C', array ('controller' => '', 'action' => ''), array(
                            'class' => 'ui-state-default fg-button',
                        ));
                    ?>
                    <?php
                        echo $this->Html->link('Privacy', array ('controller' => 'pages', 'action' => 'legal', 'privacy'), array(
                            'class' => 'ui-state-default fg-button',
                        ));
                    ?>
                    <?php
                        echo $this->Html->link('Copyright & Licensing', array ('controller' => '', 'action' => ''), array(
                            'class' => 'ui-state-default fg-button',
                        ));
                    ?>
                </div>
            </div>
        </footer>
        
    </div>
    </div></div>


</body>

</html>