<?php

include "Core_Autoloader.php";

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
        $autoload = new Core_Autoloader();
        $autoload->register("../libs");
        $autoload->register("../libs/Core");

        // include configs
        include "../configs/global.php";
        include "../configs/routes.php";

        // Run application
        try {
            $url = "/".@$_GET["url"];
            $routeParser = new Core_RouteParser(Core_Config::get("routes"),
                Core_Config::get("default.controller"), Core_Config::get("default.action"));

            $routingObject = $routeParser->parseCustom($url);

            if($routingObject == null) {
                $routingObject = $routeParser->parse($url);
            }

            Core_Route::dispatch($routingObject);
        }
        catch(Core_Exception $e) {
            $e->render();
        }
    }

}

?>