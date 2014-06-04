<ul class="systemStatus objectList">
<?php
//var_dump ($running);
if (is_array ($running)) {
    
    // Number and availability of processor threads
    echo '<li>'.$this->Icons->i('&#xe033;').' &ensp;Slots Available <span class="bigNumR">'.($numProcs['maxThreads'] - $numProcs['running']).'</span></li>';
    
    
    echo '<li>'.$this->Icons->i('&#xe026;').' &ensp;Jobs Running <span class="bigNumR">'.$numProcs['running'].'</span>';
        // Jobs which (should) be running
        if (count ($running) > 0 && $mod != 'simple') {
            echo "<div class=\"ui-helper-clearfix\"></div><ul class=\"smartbox objectList box\">";
            // Iterate over running jobs (shouldn't be any more than max processes allowed, at ToW this == only 1!)
            foreach ($running as $job) {
                echo "<li>";
                echo $this->Element ('jobs/joblist_social', compact ('job'));
                echo "</li>";
            }
            echo "</ul>";
        }
    echo "</li>";
    
    echo '<li>'.$this->Icons->i('&#xe005;').' &ensp;Jobs in Queue <span class="bigNumR">'.$numProcs['queue'].'</span>';
        // Jobs waiting to run
        if (count ($queue) > 0 && $mod != 'simple') {
            echo "<div class=\"ui-helper-clearfix\"></div><ul class=\"smartbox objectList box\">";
            // Iterate over queue jobs
            foreach ($queue as $job) {
                echo "<li>";
                echo $this->Element ('jobs/joblist_social', compact ('job'));
                echo "</li>";
            }
            echo "</ul>";
        }
    echo "</li>";
    
    if ($mod != 'simple')
        echo '<li>'.$this->Icons->i('&#xe007;').' &ensp;Memory Use <span class="bigNumR">'.(round((1-$memFree)*100)).'%</span></li>';
}
?>
</ul>