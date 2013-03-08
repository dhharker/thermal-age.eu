<?php
if (isset ($jobSections) && is_array ($jobSections))
    foreach ($jobSections as $jobs) {
        if (is_array ($jobs)) 
            echo $this->Element('jobs/joblist', array(
                'jobs' => $jobs
            ));
    }
?>