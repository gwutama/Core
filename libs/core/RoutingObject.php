<?php

/**
 * <h1>Class RoutingObject</h1>
 *
 * <p>
 * This class represents a routing URL, which controller it uses
 * and which action is called. Additionally, a RoutingObject Object
 * can contain parameters.
 * </p>
 *
 * @example
 * $route = new RoutingObject("/foo/bar", "Foo", "index", array("hello" => "bar"));
 * $route->setParamsAsGlobalVars();
 */
class RoutingObject {

    /**
     * The requested URL.
     */
    public $url;

    /**
     * Called controller name.
     */
    public $controller;

    /**
     * The action of the controller to be executed.
     */
    public $action;

    /**
     * Additional parameters. Array of mixed values.
     */
    public $params;


    /**
     * The constructor sets the member variables.
     *
     * @param $url			The requested URL.
     * @param $controller	The called controller name.
     * @param $action		The action to be executed. Defaults to "index".
     * @param $params		An array of parameters. Mixed values.
     */
    public function __construct($url, $controller,
        $action = "index", $params = array()) {

        $this->url = $url;
        $this->controller = $controller;
        $this->action = $action;
        $this->params = $params;
    }

    /**
     * Sets the parameters as global $_GET variables.
     */
    public function setParamsAsGlobalVars() {
        foreach($this->params as $key=>$value) {
            $_GET[$key] = $value;
        }
    }

}

?>