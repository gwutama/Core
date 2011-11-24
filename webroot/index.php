<?php 

include "../libs/core/Controller.php";
include "../libs/core/exceptions.php";
include "../libs/core/Request.php";
include "../libs/core/Route.php";
include "../libs/core/Template.php";
include "../libs/core/TemplateHelper.php";

define("DEFAULT_CONTROLLER", "Home");
define("DEFAULT_ACTION", "index");
define("DS", DIRECTORY_SEPARATOR);
define("RELATIVE_URL", str_replace("/webroot/index.php", "", $_SERVER['PHP_SELF']));

try {
    Route::dispatch(@$_GET["url"], DEFAULT_CONTROLLER, DEFAULT_ACTION);
}
catch(CoreException $e) {
    $e->render();
}

?>