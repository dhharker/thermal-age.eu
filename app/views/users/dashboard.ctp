<div class="grid_7">
    <div class="smartbox">
        <h1 class="sbHeading">Welcome, <?= $user['User']['alias'] ?></h1>
        <p>This page lists all your current stuff: wizard runs which you have submitted and those which are awaiting completion.</p>
    </div>
    <br />
    <?php
    if (is_array($jobSections['incomplete']) && count($jobSections['incomplete']) > 0) {
        ?>
        <div class="smartbox">
            <h2 class="sbHeading">Unfinished Jobs</h2>

            <?php
            echo $this->Element('jobs/joblist', array(
                'jobs' => $jobSections['incomplete']
            ));
            ?>
        </div>
        <?php
    }
    ?>
</div>





<div class="grid_5" style="color: ">
    <div class="smartbox">
        <h2 class="sbHeading">Recent Jobs</h2>
        <?php
        echo $this->Element('jobs/joblist', array(
            'jobs' => $jobSections['recent']
        ));
        ?>
    </div>
</div>
