<?php

namespace Core;

// Standard constant definitions
define("DS", DIRECTORY_SEPARATOR);
define("RELATIVE_URL", str_replace("/webroot/index.php", "", $_SERVER['PHP_SELF']));

/**
 * <h1>Class Core</h1>
 *
 * <p>
 * This class contains one static method to initialize and run the whole application.
 * </p>
 */
class Core {

    /**
     * Initialize the whole application.
     *
     * @static
     */
    public static function init() {
        // Include exceptions and config class
        include "../../vendors/core/exceptions.php";

        // Registers autoloaded directories
        $autoload = new Autoloader();
        $autoload->register("../../vendors/");
        $autoload->register("../");

        // load configs from yaml files
        $config = Spyc::YAMLLoad("../../configs/global.yml");
        Config::setArray($config);
        $config = Spyc::YAMLLoad("../../configs/database.yml");
        Config::setArray($config);
        $config = Spyc::YAMLLoad("../../configs/routes.yml");
        Config::setArray($config, true);

        // Run application
        try {
            $url = "/".@$_GET["url"];
            $routeParser = new RouteParser(Config::get("routes"),
                Config::get("global.defaultController"), Config::get("global.defaultAction"));

            $routingObject = $routeParser->parseCustom($url);

            if($routingObject == null) {
                $routingObject = $routeParser->parse($url);
            }

            Route::dispatch($routingObject);
        }
        catch(Exception $e) {
            $e->render();
        }
    }

}

?>