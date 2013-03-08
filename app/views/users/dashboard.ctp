<div class="grid_7">
    <div class="smartbox">
        <h1 class="sbHeading">Welcome, <?= $user['User']['alias'] ?></h1>
        <p>This page lists all your current stuff: wizard runs which you have submitted and those which are awaiting completion.</p>
    </div>
    <br />
    <div class="smartbox">
        <h2 class="sbHeading">Unfinished Jobs</h2>
        <div id="ajax-unfinished-jobs">
            <?php
            if (is_array($jobSections['incomplete']) && count($jobSections['incomplete']) > 0) {
                    echo $this->Element('jobs/joblist', array(
                        'jobs' => $jobSections['incomplete']
                    ));
                    ?>

                <?php
            }
            else {
                echo "<p>You don't currently have any saved sessions.</p>";
            }
            ?>
        </div>
        <script type="text/javascript">
            (function ($){
                $(document).ready(function () {
                    useful.ajaxReloader('#ajax-unfinished-jobs','<?=$this->Html->url(array('controller' => 'jobs', 'action' => 'job_list', 'incomplete'))?>',{
                        sinceEpoch: <?=time()?>
                    });
                });
            }(jQuery));
        </script>
    </div>
</div>





<div class="grid_5" style="color: ">
    <div class="smartbox">
        <h2 class="sbHeading">Recent Jobs</h2>
        <div id="ajax-recent-jobs">
            <?php
            echo $this->Element('jobs/joblist', array(
                'jobs' => $jobSections['recent']
            ));
            ?>
        </div>
        <script type="text/javascript">
            (function ($){
                $(document).ready(function () {
                    useful.ajaxReloader('#ajax-recent-jobs','<?=$this->Html->url(array('controller' => 'jobs', 'action' => 'job_list', 'recent'))?>',{
                        sinceEpoch: <?=time()?>
                    });
                });
            }(jQuery));
        </script>
    </div>
</div>
