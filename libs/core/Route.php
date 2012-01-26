<?php

/**
* <h1>Class Route</h1>
* 
* <p>This class represents the routing class to dispatch requests to controllers.
* Instance of this class is called by the frontend controller (index.php).
* This class handles the controller file inclusion and returning the
* template strings, based on the passed/called URL.</p>
* 
* @example
* <p>Simply use the static function dispatch()</p>
* <code>
* Route::dispatch("/foo/bar/baz/", "Home", "index");
* </code>
* 
* @author Galuh Utama
*
*/
class Route {
    /**
     * Dispatches requests to controllers.
     * Each request calls an URL, which consists of a controller name
     * and an action name.
     *
     * @param string 	$url		The called URL.
     * @param string	$controller	Controller name.
     * @param string	$action		Action name.
     * @throws ControllerNotFoundException
     * @throws ActionNotFoundException
     */
    public static function dispatch($url, $controller, $action) {
        // Handles the parameters. Explode $url separated by slashes. Example
        // url could be /foo/bar/baz/blah/. In this case, "foo" is the
        // controller name, while "bar" *could* be the action name.
        $params = explode("/", $url);

        // Parameter count is the number of exploded strings separated by
        // slashes. NOTE: every $url has to be separated by slashes.
        // So example : /foo/bar/baz/ => array("foo", "bar", "baz", "");
        // Note the empty value at the end of the array.
        $count = count($params);

        // Count == 2, means that there are only 1 parameter. This single
        // parameter must be the controller name.
        // Count > 2, means that there are more than 1 parameters.
        // The first parameter is the controller name, the second parameter
        // is the action name. The rest are the request parameters.
        if($count == 2) {
            $controller = $params[0];
        }
        elseif($count > 2) {
            $controller = $params[0];
            $action = $params[1];

            // The request parameters are the parameters except the first
            // and second parameters. Example : /foo/bar/baz/blah/test/value/
            // In this case: controller is "foo", action is "bar"
            // and request parameters are $_GET["baz"] = blah and
            // $_GET["test"] => value. We need to set these pairs into
            // php global variable $_GET.
            for($i = 2; $i < $count; $i += 2) {
                @$_GET[ $params[$i] ] = $params[$i+1];
            }
        }


        // Build $controllerClass. Class names in SSF always start with
        // uppercase. Example: class "Hello". We need to include the controller
        // class from /controllers directory. Throw exception if the class
        // cannot be found.
        $controller = ucfirst( strtolower($controller) );
        $controllerClass = $controller."Controller";
        $file = "..".DS."controllers".DS.$controllerClass.".php";

        if( !file_exists($file) ) {
            throw new ControllerNotFoundException("Controller file
                <em>$controllerClass</em> not found in <em>controllers/</em>.");
        }

        include($file);

        if( !class_exists($controllerClass) ) {
            throw new ControllerNotFoundException("Controller class
                <em>$controllerClass</em> not found in <em>controllers/</em>.");
        }

        // Assuming the class exists. Build an instance of this controller.
        // Then try to call the action methode of this controller.
        // In any case of exceptions, render the user friendly error
        // message into screen.
        try {
            $app = new $controllerClass($controller, $action);

            if( !method_exists($app, $action) ) {
                throw new ActionNotFoundException("Action <em>$action</em>
                    not found in class <em>$controller</em>.");
            }

            $app->$action();

            // Finally, render the template to be echoed by index.php.
            $app->renderTemplate();
        }
        catch(CoreException $e) {
            $e->render();
        }
    }
}

?>