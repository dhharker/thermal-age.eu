<div class="grid_12">
    <div class="smartbox">
        <h1 class="sbHeading">Welcome, <?= $user['User']['alias'] ?></h1>
        <p>This page lists all your current stuff: wizard runs which you have submitted and those which are awaiting completion.</p>
    </div>
    
</div>


<div class="grid_7">
    
    <div class="smartbox">
        <div class="floatingLoader"></div>
        <h2 class="sbHeading">Recent Jobs</h2>
        <div id="ajax-recent-jobs">
            <?php
            if (is_array($jobSections['recent']) && count($jobSections['recent']) > 0) {
                echo $this->Element('jobs/joblist', array(
                    'jobs' => $jobSections['recent']
                ));
            }
            else {
                echo "<p>Click 'Wizards' above to get started!</p>";
            }
            ?>
        </div>
        <script type="text/javascript">
            (function ($){
                $(document).ready(function () {
                    var $container = $('#ajax-recent-jobs');
                    var $loading = $('.floatingLoader',$container.parent()).first();
                    useful.ajaxReloader($container,'<?=$this->Html->url(array('controller' => 'jobs', 'action' => 'job_list', 'recent'))?>',{
                        sinceEpoch: <?=time()?>,
                        onLoading: function () {
                            $loading.hide().html('<img src="/img/loading_spinner_2.gif" alt="loading..." />').show('fade');
                        },
                        onComplete: function () {
                            $loading.hide('fade');
                        }
                    });
                });
            }(jQuery));
        </script>
    </div>
    
    <br />
    
    <div class="smartbox">
        <div class="floatingLoader"></div>
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
                    var $container = $('#ajax-unfinished-jobs');
                    var $loading = $('.floatingLoader',$container.parent()).first();
                    useful.ajaxReloader($container,'<?=$this->Html->url(array('controller' => 'jobs', 'action' => 'job_list', 'incomplete'))?>',{
                        sinceEpoch: <?=time()?>,
                        onLoading: function () {
                            $loading.hide().html('<img src="/img/loading_spinner_2.gif" alt="loading..." />').show('fade');
                        },
                        onComplete: function () {
                            $loading.hide('fade');
                        }
                    });
                });
            }(jQuery));
        </script>
    </div>
</div>


<div class="grid_5">
    <div class="smartbox">
        <div class="floatingLoader"></div>
        <h2 class="sbHeading">System Status</h2>
        <div id="ajax-system-status"><p>Updating...</p></div>
        <script type="text/javascript">
            (function ($){
                $(document).ready(function () {
                    var $container = $('#ajax-system-status');
                    var $loading = $('.floatingLoader',$container.parent()).first();
                    useful.ajaxReloader($container,'<?=$this->Html->url(array('controller' => 'jobs', 'action' => 'system'))?>',{
                        sinceEpoch: <?=time()?>,
                        onLoading: function () {
                            $loading.hide().html('<img src="/img/loading_spinner_2.gif" alt="loading..." />').show('fade');
                        },
                        onComplete: function () {
                            $loading.hide('fade');
                        }
                    });
                });
            }(jQuery));
        </script>
    </div>
    
</div>

