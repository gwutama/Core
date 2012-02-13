<?php

namespace Core;

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
     * @param $route A routing object containing routing information.
     * @throws ActionNotFoundException
     */
    public static function dispatch(RoutingObject $route) {
        // Assuming the class exists. Build an instance of this controller.
        // Then try to call the action method of this controller.
        // In any case of exceptions, render the user friendly error
        // message into screen.
        try {
            $controller = $route->controller;
            $action = $route->action;

            $controllerClass = "\\Controllers\\".$controller;
            $app = new $controllerClass($controller, $action);

            if( !class_exists($controllerClass) ) {
                throw new ControllerNotFoundException("Controller class
                <em>$controllerClass</em> not found in <em>Controllers/</em>.");
            }

            if( !method_exists($app, $route->action) ) {
                throw new ActionNotFoundException("Action <em>$action</em>
                    not found in class <em>$controller</em>.");
            }

            $app->$action();

            // Finally, render the template to be echoed by index.php.
            $app->renderTemplate();
        }
        catch(FileNotFoundException $e) {
            throw new ControllerNotFoundException("Controller file
                <em>$controllerClass</em> not found in <em>Controllers/</em>.");
        }
        catch(CoreException $e) {
            $e->render();
        }
    }
}

?>