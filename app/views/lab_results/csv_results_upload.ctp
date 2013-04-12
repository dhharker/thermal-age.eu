<div class="grid_12">
    <div class="smartbox">
        <h1 class="sbHeading">Experimental Results CSV Upload Report</h1>
        <?php
        if (isset ($etcur) && is_array ($etcur)) {
            foreach ($etcur as $ln)
                echo "<li>$ln</li>";
        }
        else {
            echo "<p class=\"error message\">Error: No results processed. ".@$etcur."</p>";
        }
        ?>
        
        <?php
        if (!empty ($job_id)) {
            echo $this->Html->link(
                $this->Icons->i('&#xe030;'). "&ensp; Return to Job Report",
                array (
                    'controller' => 'jobs',
                    'action' => 'report',
                    $job_id
                ),
                array('class' => 'fg-button ui-state-default ui-corner-all cta-button', 'escape' => false));
        }
        ?>
        
    </div>
</div>
    