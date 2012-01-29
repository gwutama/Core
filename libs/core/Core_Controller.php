<?php

/**
* <h1>Class Core_Controller</h1>
* 
* <p>
* This class represents the "CONTROLLER" part of the MVC approach. This class
* is an abstract class and should be inherited by implementing controllers.
* </p>
* 
* @example
* <code>
* try {
*	$app = new $controllerClass($controller, $action);
*
*	if( !method_exists($app, $action) ) {
*		throw new ActionNotFoundException("Action <em>$action</em>
*          	not found in class <em>$controller</em>.");
*	}
*			 
*	$app->$action();
*	$app->renderTemplate();
* }
* catch(CoreException $e) {
* 	$e->render();
* }
* </code>
* 
* @author Galuh Utama
*/
abstract class Core_Controller {
    /**
     * Defines which template this controller uses.
     *
     * @var string
     */
    protected $template;

    /**
     * The name of this controller.
     *
     * @var string
     */
    protected $name;

    /**
     * Called or requested action.
     *
     * Enter description here ...
     * @var unknown_type
     */
    protected $action;

    /**
     * Used template helpers. Template helper classes should be available
     * under libs/.
     *
     * @var array
     */
    protected $templateHelpers = array();

    /**
     * Defines the output type. Can be one of "html", "json" or "xml".
     *
     * @var string
     */
    protected $output = "html";

    /**
     * A controller must have a theme. Theme folders are located under views/.
     * If this is not defined, than the default theme will be used.
     *
     * @var string
     */
    protected $theme = "default";

    /**
     * Sets the member values and template object.
     *
     * @param string $name		The name of this controller.
     * @param string $action	Called/Requested action.
     * @throws TemplateHelperNotFoundException
     */
    public function __construct($name, $action) {
        $this->name = $name;
        $this->action = $action;

        // Initialize template object. Render error if it cannot be initialized.
        try {
            $this->template = new Core_Template($name, $action, "..".DS."views".DS.$this->theme.DS);
        }
        catch(Core_Exception $e) {
            $e->render();
        }

        // Loads and registers each template helpers. Throws exception
        // if helper cannot be found. Helper's file name should be
        // somewhat like "TemplateHelper" and should reside in libs/.
        foreach( (array) $this->templateHelpers as $helper) {
            $file = "..".DS."libs".DS."helpers".DS.$helper.".php";
            if( !file_exists($file) ) {
                throw new TemplateHelperNotFoundException("Template helper
                    <em>$helper</em> not found in <em>libs/</em>.");
            }
            include($file);
            $obj = new $helper();
            $this->template->registerHelper($obj);
        }
    }


    /**
     * Default action.
     */
    public function index() {
    }


    /**
     * Sets the template variable so it can be used in the template file.
     *
     * @param string 	$var	Variable name
     * @param mixed 	$value	Variable value
     * @see Core_Controller::get()
     * @see Core_Controller::template
     */
    public function set($var, $value) {
        $this->template->$var = $value;
    }


    /**
     * Returns the template variable. Might not be used.
     *
     * @param string $var	Variable name
     * @see Core_Controller::set()
     * @see Core_Controller::template
     * @return mixed
     */
    public function get($var) {
        return $this->template->$var;
    }


    /**
     * Method to set the output type. Output type must be one of "html" or
     * "json".
     *
     * @param string $output	Output type. "html" or "json"
     * @see Core_Controller::output
     */
    public function setOutput($output) {
        $this->output = $output;
    }


    /**
     * Renders the template object to browser.
     *
     * @see Core_Controller::setOutput()
     */
    public function renderTemplate() {
        if($this->output == "html") {
            // render file /views/theme/controller/action.tpl
            $tpl = strtolower($this->name."/".$this->action);
            echo $this->template->render($tpl);
        }
        elseif($this->output == "json") {
            echo json_encode($this->template->getVars());
        }
    }
}


?>