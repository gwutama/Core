<?php

namespace Core;

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
    private $defaultController;
    private $defaultAction;


    /**
     * Loads custom routes, probably from configs/routes.php
     *
     * @param $routes	Array of custom routes
     */
    public function __construct($routes, $defaultController = "Home", $defaultAction = "index") {
        $this->routes = $routes;
        $this->defaultController = $defaultController;
        $this->defaultAction = $defaultAction;
    }


    /**
     * Parses non-custom/standard URL into routing object.
     * Example: /controller/action/param1:value1/param2:value2/param3:value3/
     * Parameters are always in between slashes and have exact format such as param:value
     * except for the last parameter, last slash character is optional.
     *
     * @param $url  URL
     * @return RoutingObject
     */
    public function parse($url) {
        // Handles the parameters. Explode $url separated by slashes. Example
        // url could be /controller/action/param1:value1/param2:value2/param3:value3/.
        // In this case, "controller" is the controller name, while "action" *could* be the action name
        // if it is not DEFAULT_ACTION.
        $tmp = explode("/", $url);

        // Parameter count is the number of exploded strings separated by slashes.
        // Note there is an empty value at the beginning and end of the array.
        $count = count($tmp);

        if($count == 2) {
            // Count == 2 => url must be "/".
            $controller = $this->defaultController;
            $action = $this->defaultAction;
            $parameters = array();
        }
        elseif($count == 3) {
            // Count == 3, means that there are only 1 parameter. This single
            // parameter must be the controller name.
            if(!preg_match("/^[a-zA-Z0-9]+$/", $tmp[1])) {
                throw new InvalidRouteException($url);
            }

            $controller = ucwords($tmp[1]);
            $action = $this->defaultAction;
            $parameters = array();
        }
        elseif($count > 3) {
            // Count > 3, means that there are more than 1 parameters.
            // The first parameter is the controller name, the second parameter
            // is the action name. The rest are the request parameters.
            if(!preg_match("/^[a-zA-Z0-9]+$/", $tmp[1]) || !preg_match("/^[a-zA-Z0-9]+$/", $tmp[2])) {
                throw new InvalidRouteException($url);
            }

            $controller = ucwords($tmp[1]);
            $action = $tmp[2];

            // Parse parameters.
            // The request parameters are the parameters except the first
            // and second parameters. Example : /controller/action/param1:value1/param2:value2/param3:value3/
            // In this case: controller is "controller", action is "action"
            $parameters = array();
            for($i = 1; $i < $count; ++$i) {
                $params = explode(":", $tmp[$i]);

                // valid: foo:bar, foo:
                // invalid: foo:bar:baz, :foo
                if(count($params) == 2 && $params[0]) {
                    $parameters[$params[0]] = $params[1];
                }
            }
        }
        else {
            throw new InvalidRouteException($url);
        }

        // Returns new RoutingObject
        return new RoutingObject($url, $controller, $action, $parameters);
    }


    /**
     * Parses custom/non-standard URL into routing object.
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
            //    2.3. Cross check characters of the split arrays with
            //		   the actual $url. Note where the first and last positions
            //         of the $url.
            //    2.4. Based on the first and last positions, fill in the parameter
            //         values into $params.
            //    2.5. Replace each parameter values of the $url with {variable_name},
            //         save it in a temporary variable.
            //         Example above: /blah/foo/bar/etc -> /blah/{param}/{param2}/etc
            //    2.6. If $tmpVar == $url, build a RoutingObject Object then return it.
            //         Otherwise, this loop will ends after the last $route and this
            //         method will return null instead.
            $params = $this->parseParamsFromRoute($route["pattern"], $url); // Steps 1 to 2.4

            // step 2.5
            $tmp = $route["pattern"];
            foreach($params as $key=>$value) {
                $tmp = str_replace("{".$key."}", $value, $tmp);
            }

            // step 2.6
            if($tmp == $url) {
                return new RoutingObject($url, $route["controller"],
                    $route["action"], $params);
            }
        }

        return null;
    }


    /**
     * Replace words within {} of custom route with regex,
     * move the parameter name/key to an array.
     *
     * @param $route    Routing string. E.g. from routing configuration.
     * @param $url      Request URL string.
     * @return Array of parameters.
     */
    public function parseParamsFromRoute($route, $url) {
        // 1. Replace words within {} of custom route with regex,
        //    move the parameter name/key to an array.
        //    Example above: $params = array("param" => null, "param2" => null);
        // 2. To check whether $url is actually a custom route:
        //    2.1. Replace words of custom route within {} with nothing.
        //         Example above: {param} -> {}, {param2} -> {}
        //    2.2. Split the custom route based on "{}"
        //    2.3. Cross check characters of the split arrays with
        //		   the actual $url. Note where the first and last positions
        //         of the $url.
        //    2.4. Based on the first and last positions, fill in the parameter
        //         values into $params.
        $params = array();
        $len = strlen($route);
        $tokens = array();

        // 1. Get parameter names from routing first
        for($i = 0; $i < $len; $i++) {
            // Opening bracket found, now found the closing bracket
            if($route[$i] == "{") {
                $endPos = self::findClosingBracket($route, $i+1);
                $paramName = substr($route, $i+1, $endPos-$i-1);
                $params[$paramName] = null;
                $i = $endPos;
            }
        }

        // 2.1. Replace words of custom route within {} with nothing.
        $pattern = "/\{.*\}/U";
        $new = preg_replace($pattern, "{}", $route);

        // 2.2. Split the custom route based on "{}"
        $splits = explode("{}", $new);

        //   2.3. Cross check characters of the split arrays with
        //	      the actual $url. Note where the first and last positions
        //        of the $url.
        $offset = 0;
        $i = 0;
        foreach($params as &$param) {
            // Define start and end tokens
            $startToken = $splits[$i];
            if($i < count($splits)-1) {
                $endToken = $splits[$i+1];
            }
            else {
                $endToken = "";
            }

            //    2.4. Based on the first and last positions, fill in the parameter
            //         values into $params.
            $param = self::getParamBetween($url, $offset, $startToken, $endToken);

            // increment offset
            $offset += strlen($startToken) + strlen($param);

            ++$i;
        }

        return $params;
    }


    /**
     * Returns parameter value between characters.
     *
     * @static
     * @param $str          A string.
     * @param $offset       Offset.
     * @param $startToken   Opening character.
     * @param $endToken     Closing character.
     * @return String
     */
    public static function getParamBetween($str, $offset, $startToken, $endToken) {
        // empty startToken means in regex "^"
        if($startToken == "") {
            $startToken = "^";
        }

        // empty endToken means in regex "$"
        if($endToken == "") {
            $endToken = "$";
        }

        $pattern = "/" . self::escape($startToken) . "(.*)" . self::escape($endToken) . "/U";
        preg_match($pattern, $str, $matches, PREG_OFFSET_CAPTURE, $offset);

        if($matches) {
            return $matches[1][0];
        }
        return null;
    }


    /**
     * Prepends backslashes before special characters.
     *
     * @static
     * @param $str  A string
     * @return String
     */
    private static function escape($str) {
        $str = str_replace("/", "\/", $str);
        $str = str_replace(".", "\.", $str);
        $str = str_replace("(", "\(.", $str);
        $str = str_replace(")", "\).", $str);
        return $str;
    }


    /**
     * Gets the position of the closing bracket in a string,
     * starting from position 0.
     *
     * @static
     * @param $str      A string.
     * @param $startPos Start position. Integer.
     * @return Position of the closing bracket. -1 means error.
     */
    private static function findClosingBracket($str, $startPos) {
        $len = strlen($str);
        for($i = $startPos; $i < $len; $i++) {
            // return -1 if another opening bracket
            // found before a closing bracket : ERROR
            if($str[$i] == "{") return -1;
            if($str[$i] == "}") return $i;
        }

        return -1; // closing bracket was not found
    }
}

?>