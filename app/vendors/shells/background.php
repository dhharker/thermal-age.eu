<?php
/**
 * This is started by the web interface
 */
class BackgroundShell extends Shell {

    function __construct ($dispatch) {
        parent::__construct($dispatch);
        $this->Job = ClassRegistry::init('Job');
    }

    function main() {
        echo "\nStarting up...\n";
        if ($this->Job)
            return ($this->Job->tryProcessNext ());
        return false;

    }


}
?>