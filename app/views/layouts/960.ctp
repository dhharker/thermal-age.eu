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

		echo $this->Html->css('adapt/reset.css') . "\n";
        echo $this->Html->css('adapt/text.css') . "\n";
        echo $this->Html->css('taeu-jqui-theme/jquery-ui-1.8.14.custom.css') . "\n";
        echo $this->Html->css('thermal-age.css') . "\n";

    ?>
        <noscript>
            <?= $this->Html->css('adapt/mobile.css') . "\n"; ?>
        </noscript>

    <?php
        echo $this->Minify->js_link($global_minified_javascript) . "\n";
        if (isset ($minified_javascript))
            echo $this->Minify->js_link($minified_javascript) . "\n";

		echo $scripts_for_layout . "\n";

        //when debugging: (prod add to minify)
        //$this->addScript($this->Javascript->link('jqf/jquery.form.js'));
        echo $this->Javascript->link('wizard_components.js');
        echo $this->Javascript->link('ui.js');

	?>
</head>

<body>
    <div id="bg1"><div id="bg2">
    <div id="container" class="container_12 smartbox">

        <header class="grid_12" style="clear: both;">
            <div class="grid_4 alpha clearfix">
                <?php echo $this->Html->link('', '/', array('id' => 'thermalAgeLogo', 'title' => 'thermal-age.eu home')); ?>
            </div>
            <nav>
                <div class="grid_8 omega fg-buttonset fg-buttonset-single clearfix">
                    <div id="topMainMenu">
                        <?php $this->Html->link('Enter', array ('controller' => '', 'action' => ''), array('class' => 'button'/*, 'target' => '_blank'*/)); ?>
                        
                        <?php echo $this->Html->link('Home', '/', array('class' => 'fg-button ui-state-default  ui-corner-left')); ?>

                        <?php echo $this->Html->link('Clear', array ('controller' => 'wiz', 'action' => 'clearcache'), array('class' => 'fg-button ui-state-default')); ?>

                        <?php echo $this->Html->link('About', array ('controller' => 'pages', 'action' => 'about'), array('class' => 'fg-button ui-state-default ')); ?>

                        <?php echo $this->Html->link('Wizards', array ('controller' => 'wiz', 'action' => ''), array('class' => 'fg-button ui-state-default')); ?>

                        <?php echo $this->Html->link('Developers', array ('controller' => 'pages', 'action' => ''), array('class' => 'fg-button ui-state-default ')); ?>

                        <?php echo $this->Html->link('Data', array ('controller' => 'pages', 'action' => ''), array('class' => 'fg-button ui-state-default ')); ?>

                        <?php echo $this->Html->link('Help', array ('controller' => 'pages', 'action' => ''), array('class' => 'fg-button ui-state-default ')); ?>

                        <?php echo $this->Html->link('Login', array ('controller' => 'users', 'action' => 'login'), array('class' => 'fg-button ui-state-default  ui-corner-right')); ?>
                    </div>
                </div>
            </nav>
        </header>

        <div class="grid_12">
            <?php echo $this->Session->flash(); ?>
        </div>
        <div id="content" style="clear: both;">
            <?= $content_for_layout ?>
        </div>

        <!--<div id="secondary1" class="grid_3">
            <p>"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
        </div>

        <div id="mainContent" class="grid_9">
            <p>"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
            <aside class="grid_2">
                <h2>Did you know?</h2>
                <p>DNA is really long until you bake it.</p>
            </aside>
            <p>"Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>

        </div>
        -->

        

        <footer class="grid_12">
            <div class="smartbox">Copyright 2009 Your name</div>
        </footer>
    </div>
    </div></div></div>
</body>

</html>