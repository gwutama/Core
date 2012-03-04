<?php

namespace Core;

/**
 * Basic exception class. This exception class can be rendered to browser.
 * It has its own template.
 */
abstract class Exception extends \Exception {
    /**
     *
     * Enter description here ...
     */
    public function render() {
        $exception = get_class($this);
        $template = new Template("Error", $exception,
            "..".DS."..".DS."vendors".DS."app".DS."views".DS."core".DS);
        $template->message = $this->message;
        $template->exceptionClass = get_class($this);
        echo $template->render("exception");
    }
}


class InvalidArgumentException extends Exception {}


/**
 * Gets thrown when a controller is not found.
 */
class ControllerNotFoundException extends Exception {}


/**
 * Gets thrown when an action is not found.
 */
class ActionNotFoundException extends Exception {}


/**
 * Gets thrown when a template is not found.
 */
class TemplateNotFoundException extends Exception {}


/**
 * Gets thrown when a template helper is not found.
 */
class TemplateHelperNotFoundException extends Exception {}


/**
 * Gets thrown when layout is not found.
 */
class LayoutNotFoundException extends Exception {}


/**
 * Gets thrown when a configuration key is not alphanumerical.
 */
class InvalidConfigKeyException extends Exception {}


/**
 * Gets thrown when an invalid standard routing URL is found.
 */
class InvalidRouteException extends Exception {}


/**
 * Gets thrown when model cannot connect to database server.
 */
class ActiveRecordAdapterConnectionException extends Exception {}


/**
 * Gets thrown when an adaptor cannot be found.
 */
class ActiveRecordAdapterNotFoundException extends Exception {}


/**
 * Gets thrown when query cannot be successfully executed.
 */
class ActiveRecordQueryException extends Exception {}


/**
 * Gets thrown on operator errors.
 */
class ActiveRecordOperatorException extends Exception {}


/**
 * Gets thrown on model validation errors.
 */
class ActiveRecordModelValidationException extends Exception {}


/**
 * Gets thrown when a model has no adapter set.
 */
class ActiveRecordModelNoAdapterSetException extends Exception {}


/**
 * Gets thrown when invalid finder method has been called.
 */
class ActiveRecordModelFinderException extends Exception {}


/**
 * Gets thrown when file cannot be found.
 */
class FileNotFoundException extends Exception {}


/**
 * Gets thrown when service instance can't be created.
 */
class CannotCreateServiceException extends Exception {}


/**
 * Gets thrown when service is not available or not registered.
 */
class ServiceNotAvailableException extends Exception {}

?>