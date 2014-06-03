<?=$this->element('wiz/wizardControlBarTop',  array ('wizardInfos' => $wizardInfos));?>
<h1 class="sbHeading">
    Review
</h1>


<?php echo $this->Form->create('Job', array('id' => 'JobForm', 'url' => $this->here)); ?>
	<fieldset>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Job.processor_name', array (
                'type' => 'hidden',
                'default' => 'thermal_age',
                //'disabled' => true
            ));?>
        </div>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Job.parser_name', array (
                'type' => 'hidden',
                'default' => 'dna_screener',
                //'disabled' => true
            ));?>
        </div>
        <div class="grid_11 alpha">
            <?=$this->Form->input('Job.reporter_name', array (
                'type' => 'hidden',
                'default' => 'dna_screener',
                //'disabled' => true
            ));?>
        </div>
        
    </fieldset>


<p>
    All done! You can double check your entered data below and press 'Edit' if you spot any errors.
    Press <em>Continue</em> now to submit your job for processing.
</p>


<div class="smartbox" style="margin: .5em 1em;">
<?php
$dm = array (
    'specimen' => array (
        'Specimen is named %%Specimen.name%%',
        'with a lab reference of %%Specimen.code%%',
        'and is %%Temporothermal.stopdate_ybp%% years old today.',
        '%%Specimen.description%%'
    ),
    'reaction' => array (
        'The reaction to be modelled is %%Reaction.showname%%.',
        'This is a custom reaction with an activation enery of %%Reaction.ea_kj_per_mol%% kJ·mol<sup>-1</sup> and a pre-exponent of %%Reaction.f_sec%% sec.',
    ),
    'site' => array (
        'The site/area the specimen was found is %%Site.name%%',
        '(%%Site.lat_dec%%&deg;N, %%Site.lon_dec%%&deg;E).',
        'The site is elevated at %%Site.elevation%%m Above Mean Sea Level (WGS84)',
        '(source: %%Site.elevation_source%%)',
        '.',
        'Elevation data %%Site.lapse_correct%% be used to adjust temperature estimates at the site.',
        '%%Site.description%%'
    ),
    'burial' => array (
        'The specimen was excavated in %%Temporothermal.startdate_yad%%AD',
        'and was found buried under %%Burial.numLayers%% distinct layer(s).',
        '%%Temporothermal.description%%',
    ),
    'storage' => array (
        'The specimen was analysed in %%Temporothermal.startdate_yad%%AD.',
        'Between excavation and analysis, the specimen was stored at an average temperature of %%Temporothermal.temp_mean_c%%&deg;C with temperature variation over a range of %%Temporothermal.temp_pp_amp_c%%&deg;C.',
    ),
);
$convert = array ( // !!! is in format step -> field NOT stop -> model -> field
    'specimen' => array (
        'stopdate_ybp' => function ($bp) {
            return $bp + (intval(date('Y'))-1950);
        }
    ),
    'site' => array (
        'lat_dec' => function ($lat) { return sprintf ('%.03f',$lat); },
        'lon_dec' => function ($lon) { return sprintf ('%.03f',$lon); },
        'lapse_correct' => function ($l) { return ($l == 1) ? 'will' : 'will not'; }
    )       
);
    $stepNum=0;
    foreach ($input as $stepName => $models) {
        $stepNum++;
        //$sTitle = (isset ($dm[$stepName])) ? $dm[$stepName] : $stepName;
        $sTitle = Inflector::humanize ($stepName);
        echo "<h3 class=\"sbHeading\" style=\"clear: none; border-top: 1px solid #ccc;\">$sTitle</h3>".
            "<div style=\"float: right;\">".$this->Html->link(
                $this->Icons->i('&#xe04f;').' &ensp;Edit',
                array ('controller' => 'wiz', 'action' => '', $stepName),
                array('class' => 'fg-button ui-corner-all ui-state-default', 'escape' => false, 'style' => 'margin: 2px 1em; clear: none; ')
            )."</div>"    
            ."<ul style=\"margin: 0 3% 2% 1%;\"><li>"
        ;
        $lfr = '';
        if (isset ($dm[$stepName])) foreach ($dm[$stepName] as $frag) {
            
            $div = (preg_match ("/\.\s*$/", $lfr) == 1) ? "</li><li>" : '';

            $lfr = $frag;
            if (preg_match_all ('/%%(([a-z0-9_]+)\.([a-z0-9_]+))%%/i', $frag, $m, PREG_SET_ORDER) > 0) {
                $op = true;
                foreach ($m as $f) {
                    @$v = (isset ($convert[$stepName])
                            && isset ($convert[$stepName][$f[3]])
                            && is_callable($convert[$stepName][$f[3]])) ?
                        $convert[$stepName][$f[3]]($models[$f[2]][$f[3]]) : $models[$f[2]][$f[3]];
                    //echo "{$models[$f[2]][$f[3]]} = $v<br />";
                    if (!isset ($models[$f[2]][$f[3]]))
                        $op = false;
                    elseif ($op !== false && strlen ($v) > 0) $frag = str_replace ($f[0], "<strong>".$v. "</strong> ", $frag) . " ";
                    elseif ($op !== false) $frag = str_replace ($f[0], "", $frag);
                }
                if ($op !== false && strlen ($frag) > 0)
                    echo $div . $frag;
            }
            
        }
        echo "</li></ul>";
        
    }
    /*foreach ($input as $stepName => $models) {
        echo "<li>$stepName:<ol>";
        foreach ($models as $modelName => $modelValues) {
            echo "<li>$modelName:<ol>";
            foreach ($modelValues as $fieldName => $fieldValue) {
                echo "<li><strong>$fieldName</strong>: " . ((is_string($fieldValue)) ? $fieldValue : print_r ($fieldValue, TRUE)) . "</li>";
            }
            echo "</ol></li>";
        }
        echo "</ol></li>";
    }//*/

?>
</div>


<?=$this->element('wiz/wizardControlBar',  array ('wizardInfos' => $wizardInfos));?>

<?php echo $this->Form->end(); ?>

<script type="text/javascript">
$(document).ready (function () {
    wc.initReviewForm ();
});
</script>



