<?php
/**
 * This is started a) by the web interface b) by CRON
 */
class BackgroundShell extends Shell {

    function __construct () {
        $this->Job = ClassRegistry::init('Job');
    }

    function main() {

        if ($this->Job)

        $i = 60;
        while ($i-- > 0) {
        echo ".";
        sleep (1);
        }
    }


}
?>