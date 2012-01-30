<?php

/**
 * Basic exception class. This exception class can be rendered to browser.
 * It has its own template.
 */
class Core_Exception extends Exception {
    /**
     *
     * Enter description here ...
     */
    public function render() {
        $exception = get_class($this);
        $template = new Core_Template("Error", $exception, "..".DS."views".DS."core".DS);
        $template->message = $this->message;
        $template->exceptionClass = get_class($this);
        echo $template->render("exception");
    }
}


/**
 * Gets thrown when a controller is not found.
 */
class ControllerNotFoundException extends Core_Exception {}


/**
 * Gets thrown when an action is not found.
 */
class ActionNotFoundException extends Core_Exception {}


/**
 * Gets thrown when a template is not found.
 */
class TemplateNotFoundException extends Core_Exception {}


/**
 * Gets thrown when a template helper is not found.
 */
class TemplateHelperNotFoundException extends Core_Exception {}


/**
 * Gets thrown when layout is not found.
 */
class LayoutNotFoundException extends Core_Exception {}


/**
 * Gets thrown when a configuration key is not alphanumerical.
 */
class InvalidConfigKeyException extends Core_Exception {}


/**
 * Gets thrown when an invalid standard routing URL is found.
 */
class InvalidRouteException extends Core_Exception {}


/**
 * Gets thrown when model cannot connect to database server.
 */
class ActiveRecordAdapterConnectionException extends Core_Exception {}


/**
 * Gets thrown when an adaptor cannot be found.
 */
class ActiveRecordAdapterNotFoundException extends Core_Exception {}

?>