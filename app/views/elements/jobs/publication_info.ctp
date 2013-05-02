    <div class="grid_12 ">
        <div class="smartbox">
            <h2 class="sbHeading ui-corner-tl">
                <?=$this->Icons->i('&#xe020;')?> Publication
            </h2>
            <div>
                <?
                if (!!$job['Job']['published'] && strtotime($job['Job']['published_date']) < time()) {
                    echo "<p>This job is published and publicly accessible from the following URL: ";
                    $jurls = array (
                        'controller' => 'jobs',
                        'action' => 'published',
                        $job['Job']['pub_ref']
                    );
                    
                    $jurl = $this->Html->url ($jurls, true);
                    echo $this->Html->link ($jurl, $jurl);
                    echo "</p>";
                }
                else {
                    echo $this->Element('jobs/publish_job_form', compact('job'));
                }
                ?>
            </div>
        </div>
    </div>