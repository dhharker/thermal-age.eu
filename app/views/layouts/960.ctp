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

        echo $this->Html->css('thermal-age.css') . "\n";

    ?>
        <noscript>
            <?= $this->Html->css('adapt/mobile.css') . "\n"; ?>
        </noscript>

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" type="text/javascript"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js" type="text/javascript"></script>
    <?php
        //when debugging: (prod add to minify)
        //$this->addScript($this->Javascript->link('jqf/jquery.form.js'));
        echo $this->Javascript->link('wizard_components.js');

        echo $this->Minify->js_link($global_minified_javascript) . "\n";
        if (isset ($minified_javascript))
            echo $this->Minify->js_link($minified_javascript) . "\n";

		echo $scripts_for_layout . "\n";

	?>
</head>

<body>
    <div id="container" class="container_12">
        <header class="grid_12">
            <nav>
                <ul>
                    <li>Home</li>
                </ul>
            </nav>
        </header>

        <section>

            <div id="secondary1" class="grid_3">
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

                


        </section>

        

        <footer class="grid_12">
            <p>Copyright 2009 Your name</p>
        </footer>
    </div>
</body>

</html>