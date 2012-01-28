<?php 

include "../libs/core/Controller.php";
include "../libs/core/exceptions.php";
include "../libs/core/Request.php";
include "../libs/core/RoutingObject.php";
include "../libs/core/Route.php";
include "../libs/core/RouteParser.php";
include "../libs/core/Template.php";
include "../libs/core/TemplateHelper.php";

include "../configs/routes.php";

define("DEFAULT_CONTROLLER", "Home");
define("DEFAULT_ACTION", "index");
define("DS", DIRECTORY_SEPARATOR);
define("RELATIVE_URL", str_replace("/webroot/index.php", "", $_SERVER['PHP_SELF']));

try {
    $routeParser = new RouteParser($routes);
    $routingObject = new RoutingObject(@$_GET["url"], DEFAULT_CONTROLLER, DEFAULT_ACTION);
    if($customRoute = $routeParser->parseCustom($url)) {
        Route::dispatchCustom($customRoute);
    }
    else {
        Route::dispatch($routingObject);
    }
}
catch(CoreException $e) {
    $e->render();
}

?>