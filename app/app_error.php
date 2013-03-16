<?php

class AppError extends ErrorHandler {
    
    // Big thanks to http://www.bradezone.com/2009/05/21/cakephp-beforefilter-and-the-error-error/
    // Fixes the fact that cake 1.3 doesn't run app controller's beforeFilter on error pages!
    function _outputMessage($template) {
        $this->controller->beforeFilter();
        parent::_outputMessage($template);
    }
}

?>