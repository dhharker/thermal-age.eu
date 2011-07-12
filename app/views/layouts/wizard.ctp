<!DOCTYPE html>

<html lang="en">

    <head>
        <title><?php echo $title_for_layout?></title>
        <link rel="shorcut icon" type="image/x-ico" href="/favicon.ico" />
        <link rel="stylesheet" href="/css/reset-min.css" type="text/css" media="screen" charset="utf-8">
        <link rel="stylesheet" href="/css/thermal-age-wizard.css" type="text/css" media="screen" charset="utf-8">
        <?php $this->Minify->link($minified_javascript); ?>
        <?php /*echo $scripts_for_layout*/ ?>

        
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