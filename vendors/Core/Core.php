<?php

namespace Core;

use Core\Utility\Spyc;
use Core\Storage\Config;
use Core\Routing\Route;
use Core\Routing\RouteParser;
use Core\Exception;

// Include exceptions
include "../../vendors/core/exceptions.php";

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
     * Actual version
     */
    const version = "0.1";


    /**
     * Initialize the whole application.
     *
     * @static
     */
    public static function init() {
        // Registers autoloaded directories
        $autoload = new Autoloader();
        $autoload->register("../../vendors/");
        $autoload->register("../../vendors/app/");
        $autoload->register("../");
        $autoload->register("../libs/");

        // load configs from yaml files
        $config = Spyc::YAMLLoad("../../configs/global.yml");
        Config::setArray($config);
        $config = Spyc::YAMLLoad("../../configs/database.yml");
        Config::setArray($config);
        $config = Spyc::YAMLLoad("../../configs/routes.yml");
        Config::setArray($config, true);

        // Set configs for template paths
        Config::set("global.template.baseDir", "../views/");
        Config::set("global.template.overrideBaseDir", "../../vendors/app/views/");

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