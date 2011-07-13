<!DOCTYPE html>

<html lang="en">

    <head>
        <title><?php echo $title_for_layout?></title>

        <link rel="stylesheet" href="/css/reset-min.css" type="text/css" media="screen" charset="utf-8">
        <link rel="stylesheet" href="/css/thermal-age-wizard.css" type="text/css" media="screen" charset="utf-8">

        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js" type="text/javascript"></script>
        <script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.14/jquery-ui.min.js" type="text/javascript"></script>


        <?php /*echo*/ $this->Minify->js_link($minified_javascript); ?>
        <?php echo $scripts_for_layout ?>

        
    </head>
    <body>
        <div id="gradientContainer"><div id="mainWindow">
            <div id="central">
                
                <div id="wizardControlBox" class="smartBox centreFloater">
                    
                    
                    <?php echo $content_for_layout ?>
                    

                </div>
            </div>
        </div></div>
    </body>
</html>