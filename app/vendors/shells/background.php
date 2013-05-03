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
        
        // Build a closure to allow the pdf generator functions to render views from within the model
        $renderer = function ($arrOpts) {
            App::import('Core', 'Controller');
            App::import('Controller', 'Jobs');

            $Jobs = new JobsController;
            $Jobs->constructClasses();

            return $Jobs->_render ($arrOpts);
        };
        
        if ($this->Job) {
            $this->Job->_set_renderer_closure ($renderer);
            return ($this->Job->tryProcessNext ());
        }
        return false;

    }
    
    


}
?>