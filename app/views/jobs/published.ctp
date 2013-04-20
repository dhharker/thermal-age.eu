<div class="grid_12 ">
    <div class="smartbox">
        <?php
            if (isset ($job) && is_array ($job) && !empty ($job)) {
                // Some job has been found
                if (isset ($job['Job']['authorised']) && !$job['Job']['authorised']) {
                    echo '<h1 class="sbHeading ui-corner-tl">Not authorised</h1>';
                    echo "<p>This job has not been published and you do not own this job (".print_r($job['Job']['authorised_message'],1).").</p>";
                }
                elseif (isset ($job['Job']['embargo']) && !!$job['Job']['embargo']) {
                    echo '<h1 class="sbHeading ui-corner-tl">Publication Embargo</h1>';
                    echo "<p>This job is pending release. It will be available on: {$job['Job']['published_date']}.</p>";
                }
            }
            else {
                $this->cakeError ('error404');
            }
        ?>
    </div>
</div>