<!DOCTYPE html>

<html lang="en">

    <head>
        <title><?php echo $title_for_layout?></title>
        <link rel="stylesheet" href="/assets/css/reset-min.css" type="text/css" media="screen" charset="utf-8">
        <link rel="stylesheet" href="/assets/css/thermal-age-wizard.css" type="text/css" media="screen" charset="utf-8">
        <?php echo $scripts_for_layout ?>
    </head>
    <body>
        <div id="gradientContainer"><div id="mainWindow">
            <div id="central">
                
                <div id="wizardControlBox" class="smartBox centreFloater">
                    
                    
                    <div id="wizardScreen" class="smartBox wizScreenPos">
                        <?php echo $content_for_layout ?>

                    </div>

                    <div id="wizardRightColumn">
                        <a href="/" id="thermalAgeLogo"></a>
<!--                        <a href="#somewhereelse6" id="synthesysLogo"></a>-->
                        <h3>Wizard Progress:</h3>
                        <ul class="menu">
                            <li><a href="#here1" class="complete" title=""><div class="icon"></div>Specimen Name <div class="blurbCon">&rArr; <strong>Pickled monkey brains #1 overlong...</strong></div></a></li>
                            <li><a href="#here1" class="complete" title=""><div class="icon"></div>Model Reaction <div class="blurbCon">&rArr; DNA Depurination</div></a></li>
                            <li><a href="#here2" class="error" title=""><div class="icon"></div>Site Location <div class="blurbCon">&rArr; Error(s) - please amend!</div></a></li>
                            <li><a href="#here3" class="complete" title=""><div class="icon"></div>Specimen Age <div class="blurbCon">&rArr; Deposited: 4500 BCE</div></a></li>
                            <li><a href="#here3" class="complete" title=""><div class="icon"></div>Excavation Date <div class="blurbCon">&rArr; Excavated: AD 2011</div></a></li>
                            <li><a href="#here5" class="current" title="">Burial</a></li>
                            <li><a href="#here5" class="future" title="">Storage</a></li>
                            
                        </ul>

                        <div style="height: 1em;"></div>


                        <ul class="options menu" style="position: absolute; bottom: 1em;">
                            
                            <li><a href="#here1" title=""><div class="icon"></div>Help <div class="blurbCon">...for if you get stuck</div></a></li>
                            <li><a href="#here1" title=""><div class="icon"></div>Login <div class="blurbCon">...to get MORE</div></a></li>
                            <li><a href="#here1" title=""><div class="icon"></div>Feedback <div class="blurbCon">...helps make this site better!</div></a></li>

                            <li><a href="http://www.synthesys.info/II_JRA_1.htm" title="Part of the SYNTHESYS project" id="synthesysLogoLink">&nbsp;</a></li>
                        </ul>
                    </div>
                    
                    <div id="pageLinks">
                        <ul>
                            <li><a class="current" href="#somewhereelse1">home</a></li>
                            <li><a href="#somewhereelse2">about</a></li>
                            <li><a href="#somewhereelse3">legal</a></li>
                            <li><a href="#somewhereelse4">citing</a></li>
                            <li><a href="#somewhereelse5">help</a></li>
                            <li><a href="#somewhereelse6">developers</a></li>
                        </ul>
                        
                    </div>
                    

                </div>
            </div>
        </div></div>
    </body>
</html>