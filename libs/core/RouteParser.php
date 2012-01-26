<?php

/**
 * <h1>Class RouteParser</h1>
 *
 * <p>
 * Custom routes predefined in configs/routes.php
 * This should be called before dispatching requests to Route Object.
 * </p>
 *
 * @example
 * $url = "/foo/1/hello/world";
 * $routeParser = new RouteParser($routes);
 * if($customRoute = $routeParser->parseCustom($url)) {
 *		Route::dispatchCustom($customRoute);
 * }
 * else {
 * 		$route = new Route($url, $controller, $action);
 * 		Route::dispatch($route);
 * }
 */
class RouteParser {

    /**
     * Custom routes from configs/routes.php
     */
    private $routes;


    /**
     * Loads custom routes, probably from configs/routes.php
     *
     * @param $routes	Array of custom routes
     */
    public function __construct($routes) {
        $this->routes = $routes;
    }


    /**
     * Identifies whether an URL is a custom URL or not.
     *
     * @param $url	URL
     * @return RoutingObject
     */
    public function parseCustom($url) {
        // Detects whether this URL is in RouteParser::routes
        foreach($this->routes as $route) {
            // Routes are in this format /blah/{param}/{param2}/etc
            // Example $url would be /blah/foo/bar/etc
            //
            // Strategy:
            // 1. Replace words within {} of custom route with regex,
            //    move the parameter name/key to an array.
            //    Example above: $params = array("param" => null, "param2" => null);
            // 2. To check whether $url is actually a custom route:
            //    2.1. Replace words of custom route within {} with nothing.
            //         Example above: {param} -> {}, {param2} -> {}
            //    2.2. Split the custom route based on "{}"
            //    2.3. Cross check characters of the spllited arrays with
            //		   the actual $url. Note where the first and last positions
            //         of the $url.
            //    2.4. Based on the first and last positions, fill in the parameter
            //         values into $params.
            //    2.5. Replace each paameter values of the $url with {variable_name},
            //         save it in a temporary variable.
            //         Example above: /blah/foo/bar/etc -> /blah/{param}/{param2}/etc
            //    2.6. If $tmpVar == $url, build a RoutingObject Object then return it.
            //         Otherwise, this loop will ends after the last $route and this
            //         method will return null instead.
            $params = $this->parseParamsFromRoute($route);
        }

        return null;
    }


    /**
    * Replace words within {} of custom route with regex,
    * move the parameter name/key to an array.
    */
    public function parseParamsFromRoute($route) {
    }
}

?>