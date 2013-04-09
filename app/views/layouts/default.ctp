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
        echo $this->Html->css('chosen.css') . "\n";
        echo $this->Html->css('imi.css') . "\n";
        ?>
        <link href='http://fonts.googleapis.com/css?family=Cantarell:400,700,400italic,700italic' rel='stylesheet' type='text/css'>
        <?php

        if (isset ($isWizard) && $isWizard == TRUE) {
            echo $this->Html->css('thermal-age-wizard.css') . "\n";
        }

        // this is only the default for when js is disabled, sizes otherwise in config.js
        $cssSize = (isset ($isMobile) && $isMobile) ? 'mobile' : '960';

        
    ?>

    <!-- iPhone & pals -->
    <meta name="viewport" content="initial-scale = 1.0">

    <noscript>
        <?= $this->Html->css('adapt/' . $cssSize . '.min.css') . "\n"; ?>
    </noscript>
            
        

    <?php
        if (isset ($global_minified_javascript))
            echo $this->Minify->js_link($global_minified_javascript) . "\n";
        if (isset ($minified_javascript))
            echo $this->Minify->js_link($minified_javascript) . "\n";
        
        
        //$this->addScript ($global_javascript);
        if (isset ($global_javascript))
            echo $this->Html->script($global_javascript);
        echo $scripts_for_layout;
        
        //when debugging: (prod add to minify)
        //$this->addScript($this->Javascript->link('jqf/jquery.form.js'));


        // @todo move this to $minified_javascript via wizController::_initialiseWizardEnvironment()
        if (isset ($isWizard) && $isWizard == TRUE) {
            echo $this->Javascript->link('wizard_components.js');
        }
        
        $ll = $this->Javascript->link('lte-ie7.js');
        echo "<!--[if lte IE 7]>".$ll."<![endif]-->";

        echo $this->Javascript->link('ui.js');

        // looks like this might have to be loaded last of all to make the callbacks work nicely
        //echo $this->Javascript->link('adapt/adapt.js');



	?>
</head>

<body>
    <div id="bg1"></div> <div id="bg2"></div>

    <div id="container" class="container_12 ui-corner-bottom" style="margin-bottom: 2em;">

        <header class="grid_12 smart ui-corner-bottom" style="clear: both;">
            <div class="grid_3 alpha clearfix no-v-margin">
                <?php echo $this->Html->link('', '/', array('id' => 'thermalAgeLogo', 'title' => 'thermal-age.eu home')); ?>
            </div>
            <nav>
                <div class="grid_9 omega fg-buttonset fg-buttonset-single clearfix no-v-margin">
                    <div id="topMainMenu" class="NUBs">
                        <?php
                        $logged_in = (isset ($logged_in_user) && is_array ($logged_in_user) && isset ($logged_in_user['User']['id']) && !!$logged_in_user['User']['id']) ? true : false;
                        ?>
                        <?php $this->Html->link('Enter', array ('controller' => '', 'action' => ''), array('class' => 'button'/*, 'target' => '_blank'*/)); ?>
                        
                        <?php echo $this->Html->link($this->Icons->i('&#xe000;').'Home', '/', array('escape' => false, 'class' => 'fg-button ui-state-default  ui-corner-left')); ?>

                        <?php echo $this->Html->link($this->Icons->i('&#xe064;').'About', array ('controller' => 'pages', 'action' => 'about'), array('escape' => false, 'class' => 'fg-button ui-state-default ')); ?>
                        

                        <?php echo $this->Html->link($this->Icons->i('&#xe009;').'Wizards', array ('controller' => 'wiz', 'action' => 'index'), array('escape' => false, 'class' => 'fg-button ui-state-default')); ?>
                        <?php if (0&&$logged_in) echo $this->Html->link($this->Icons->i('&#xe052;').'Clear', array ('controller' => 'wiz', 'action' => 'clearcache'), array('escape' => false, 'class' => 'fg-button ui-state-default')); ?>
                        
                        <?php if ($logged_in) echo $this->Html->link($this->Icons->i('&#xe069;').'Dashboard', array ('controller' => 'users', 'action' => 'dashboard'), array('escape' => false, 'class' => 'fg-button ui-state-default')); ?>

                        <?php  if (!$logged_in) echo $this->Html->link($this->Icons->i('&#xe00e;').'Login', array ('controller' => 'users', 'action' => 'login'), array('escape' => false, 'class' => 'fg-button ui-state-default')); ?>
                        <?php  if ($logged_in) echo $this->Html->link($this->Icons->i('&#xe041;').'Logout', array ('controller' => 'users', 'action' => 'logout'), array('escape' => false, 'class' => 'fg-button ui-state-default')); ?>
                        
                        <?php echo $this->Html->link($this->Icons->i('&#xe04a;').'Feedback', array ('controller' => 'feedback', 'action' => ''), array('escape' => false, 'class' => 'fg-button ui-state-default feedbackButton ui-corner-right', 'id' => 'feedbackButton')); ?>

                        <?php  $this->Html->link($this->Icons->i('&#xe071;').'Developers', array ('controller' => 'pages', 'action' => ''), array('escape' => false, 'class' => 'fg-button ui-state-default ')); ?>

                        <?php  $this->Html->link($this->Icons->i('&#xe073;').'Data', array ('controller' => 'pages', 'action' => ''), array('escape' => false, 'class' => 'fg-button ui-state-default ')); ?>

                        <?php  $this->Html->link($this->Icons->i('&#xe067;').'Help', array ('controller' => 'pages', 'action' => 'help'), array('escape' => false, 'class' => 'fg-button ui-state-default ')); ?>


                    </div>
                </div>
            </nav>
        </header>

        <div id="pageContent" style="clear: both;">
            <?php echo $this->Session->flash(); ?>
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
            <div class="smartsharp ui-corner-top">
                <div class="grid_6 alpha clearfix no-v-margin">
                    <div class="footerText">
                        &copy; Copyright 2009&ndash;<?= date ('Y') ?>
                    </div>

                </div>
                <div class="grid_6 omega clearfix no-v-margin">
                    <?php
                        echo $this->Html->link('T&C', array ('controller' => 'pages', 'action' => 'legal', 'terms'), array(
                            'class' => 'ui-state-default fg-button',
                        ));
                    ?>
                    <?php
                        echo $this->Html->link('Privacy', array ('controller' => 'pages', 'action' => 'legal', 'privacy'), array(
                            'class' => 'ui-state-default fg-button',
                        ));
                    ?>
                    <?php
                        echo $this->Html->link('Copyright & Licensing', array ('controller' => 'pages', 'action' => 'legal', 'copyright'), array(
                            'class' => 'ui-state-default fg-button',
                        ));
                    ?>
                </div>
            </div>
        </footer>
        
    </div>
    <div class="ui-helper-clearfix"></div>

    
    <?php if (configure::read('debug') >= 2) { ?> 
    <div id='sqldebugtoggle'> 
        <a id="dbgtoggle">[Expand/Collapse SQL]</a> 
        <script language="javascript"><!-- 
        (function ($) {
            $('table.cake-sql-log').hide();
            $('#dbgtoggle').click(function () {
                $('table.cake-sql-log').toggle();
            });
        }(jQuery));
        --></script> 
    </div> 
    <?php echo $this->element('sql_dump'); ?>

    <?php } ?> 
    
    <script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-39172611-1']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>
</body>

</html>