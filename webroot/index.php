<?php 

include "../libs/core.php";

define("DEFAULT_CONTROLLER", "Home");
define("DEFAULT_ACTION", "index");
define("DS", DIRECTORY_SEPARATOR);

try {
    Route::dispatch(@$_GET["url"], DEFAULT_CONTROLLER, DEFAULT_ACTION);
}
catch(CoreException $e) {
    $e->renderHtml();
}

?>