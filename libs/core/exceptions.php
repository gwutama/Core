<?php

/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
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
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class ControllerNotFoundException extends CoreException {}



/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class ActionNotFoundException extends CoreException {}



/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class TemplateNotFoundException extends CoreException {}



/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class TemplateHelperNotFoundException extends CoreException {}



/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class LayoutNotFoundException extends CoreException {}

?>