<?php

/**
* Class Route
* This class represents the routing class to dispatch requests to controllers.
* @author Galuh Utama
*
*/
class Route {
	/**
	 * Dispatches requests to controllers.
	 * Each request calls an URL, which consists of a controller name
	 * and an action name.
	 *
	 * @param string 	$url		The called URL.
	 * @param string	$controller	Controller name.
	 * @param string	$action		Action name.
	 * @throws ControllerNotFoundException
	 * @throws ActionNotFoundException
	 */
	public static function dispatch($url, $controller, $action) {
		$params = explode("/", $url);
		$count = count($params);

		if($count == 2) {
			$controller = $params[0];
		}
		elseif($count > 2) {
			$controller = $params[0];
			$action = $params[1];
			for($i = 2; $i < $count; $i += 2)
			@$_GET[ $params[$i] ] = $params[$i+1];
		}

		$controller = ucfirst( strtolower($controller) );
		$controllerClass = $controller."Controller";
		$file = "..".DS."controllers".DS.$controllerClass.".php";

		if( !file_exists($file) ) {
			throw new ControllerNotFoundException("Controller file
            	<em>$controllerClass</em> not found in <em>controllers/</em>.");
		}

		include($file);

		if( !class_exists($controllerClass) ) {
			throw new ControllerNotFoundException("Controller class
            	<em>$controllerClass</em> not found in <em>controllers/</em>.");
		}

		try {
			$app = new $controllerClass($controller, $action);

			if( !method_exists($app, $action) ) {
				throw new ActionNotFoundException("Action <em>$action</em>
    	        	not found in class <em>$controller</em>.");
			}
			 
			$app->$action();
			$app->renderTemplate();
		}
		catch(CoreException $e) {
			$e->render();
		}
	}
}

?>