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
        foreach($this->routes as $route=>$routingConfigs) {
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
            $params = $this->parseParamsFromRoute($route, $url); // Steps 1 to 2.4

            // step 2.5
            $tmp = $route;
            foreach($params as $key=>$value) {
                $tmp = str_replace("{$key}", $value, $tmp);
            }

            // step 2.6
            if($tmp == $url) {
                return new RoutingObject( $routingConfigs["controller"],
                    $routingConfigs["action"], $params );
            }
            return null;
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
        $params = array();
        $len = strlen($route);
        $tokens = array();

        // Get parameter names from routing first
        for($i = 0; $i < $len; $i++) {
            // Opening bracket found, now found the closing bracket
            if($route[$i] == "{") {
                $endPos = self::findClosingBracket($route, $i+1);
                $paramName = substr($route, $i+1, $endPos-$i-1);
                $params[$paramName] = null;
                $i = $endPos;
            }
        }

        // get tokens
        // find strings between :
        // 1. "" and "{"
        // 2. "}" and "{"
        // 3. "}" and ""

        // 1. "" and "{"
        $pattern = "/^(.*){/U";
        preg_match($pattern, $route, $matches, PREG_OFFSET_CAPTURE);
        $tokens[] = $matches[1];

        // 2. "}" and "{"
        $pattern = "/}(.*){/U";
        preg_match_all($pattern, $route, $matches, PREG_OFFSET_CAPTURE);
        foreach($matches[1] as $match) {
            $tokens[] = $match;
        }

        // 3. "}" and ""
        $pattern = "/.*}(.*)$/";
        preg_match($pattern, $route, $matches, PREG_OFFSET_CAPTURE);
        $matches[1][0] .= "$";
        $tokens[] = $matches[1];

        // now fill parameter values
        $offset = 0;

        // $tokens[$i][1] = offset position, $tokens[$i][0] = string
        $i = 0;
        foreach($params as &$param) {
            $param = self::getParamBetween(&$url, &$offset, $tokens[$i], $tokens[$i+1]);
            $i++;
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
    private static function getParamBetween($str, $offset, $startToken, $endToken) {
        // $startToken[1] = offset position, $startToken[0] = string
        // $endToken idem
        $pattern = "/" . self::escape($startToken[0]) . "(.*)" . self::escape($endToken[0]) . "/U";
        preg_match($pattern, $str, $matches, PREG_OFFSET_CAPTURE, $offset);
        $offset = $matches[1][1];
        //var_dump($pattern, $offset);
        //var_dump($matches);
        return $matches[1][0];
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