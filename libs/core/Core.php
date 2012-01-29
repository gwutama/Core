<?php

include "Autoloader.php";

class Core {

    /**
     * Initialize the whole application.
     *
     * @static
     */
    public static function init() {
        // Standard constant definitions
        define("DS", DIRECTORY_SEPARATOR);
        define("RELATIVE_URL", str_replace("/webroot/index.php", "", $_SERVER['PHP_SELF']));

        // Include exceptions and config class
        include "../libs/core/exceptions.php";

        // Registers autoloaded directories
        $autoload = new Autoloader();
        $autoload->register("../libs/core");

        // include configs
        include "../configs/global.php";
        include "../configs/routes.php";

        // Run application
        try {
            $url = "/".@$_GET["url"];
            $routeParser = new RouteParser(Config::get("routes"),
                Config::get("default.controller"), Config::get("default.action"));

            $routingObject = $routeParser->parseCustom($url);

            if($routingObject == null) {
                $routingObject = $routeParser->parse($url);
            }

            Route::dispatch($routingObject);
        }
        catch(CoreException $e) {
            $e->render();
        }
    }

}

?>