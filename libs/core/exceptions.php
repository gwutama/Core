<?php

/**
 * Basic exception class. This exception class can be rendered to browser.
 * It has its own template.
 */
class CoreException extends Exception {
    /**
     *
     * Enter description here ...
     */
    public function render() {
        $exception = get_class($this);
        $template = new Template("Error", $exception, "..".DS."views".DS);
        $template->message = $this->message;
        $template->exceptionClass = get_class($this);
        echo $template->render("core.exception");
    }
}


/**
 * Gets thrown when a controller is not found.
 */
class ControllerNotFoundException extends CoreException {}


/**
 * Gets thrown when an action is not found.
 */
class ActionNotFoundException extends CoreException {}


/**
 * Gets thrown when a template is not found.
 */
class TemplateNotFoundException extends CoreException {}


/**
 * Gets thrown when a template helper is not found.
 */
class TemplateHelperNotFoundException extends CoreException {}


/**
 * Gets thrown when layout is not found.
 */
class LayoutNotFoundException extends CoreException {}


/**
 * Gets thrown when a configuration key is not alphanumerical.
 */
class InvalidConfigKeyException extends CoreException {}


/**
 * Gets thrown when an invalid standard routing URL is found.
 */
class InvalidRouteException extends CoreException {}

?>