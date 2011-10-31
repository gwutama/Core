<?php

class Route {
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
            throw new ControllerNotFoundException("Controller file <em>$controllerClass</em> not found in <em>controllers/</em>.");
        }                
        include($file);
        if( !class_exists($controllerClass) ) {
            throw new ControllerNotFoundException("Controller class <em>$controllerClass</em> not found in <em>controllers/</em>.");
        }        
        $app = new $controllerClass($controller, $action);
        if( !method_exists($app, $action) ) {
            throw new ActionNotFoundException("Action <em>$action</em> not found in class <em>$controller</em>.");
        }
        $app->$action();
    }
}


class Controller {
    protected $template;
    protected $name;
    protected $action;
    protected $templateHelpers = array();
    
    public function __construct($name, $action) {
        $this->name = $name;
        $this->action = $action;
        $this->template = new Template($name, $action, "..".DS."views".DS);
        foreach( (array) $this->templateHelpers as $helper) {
            $file = "..".DS."libs".DS.$helper.".php";
            if( !file_exists($file) ) {
                throw new TemplateHelperNotFoundException("Template helper <em>$helper</em> not found in <em>libs/</em>.");        
            }            
            include($file);
            $obj = new $helper();
            $this->template->registerHelper($obj);
        }
    }
    
    public function index() {}
    
    public function set($var, $value) {
        $this->template->$var = $value;
    }
    
    public function get($var) {
    	return $this->template->$var;
    }
    
    public function __destruct() {
        $tpl = strtolower($this->name.".".$this->action);
        echo $this->template->render($tpl);
    }    
}


class Template {
    private $baseDir;
    private $vars = array();
    private $templateHelpers = array();
    private $layout = "default";
    private $title;
    private $controller;
    private $action;
    private $styles;
    private $scripts;
    
    public function __construct($controller, $action, $baseDir) {
    	$this->controller = $controller;
    	$this->action = $action;
        $this->baseDir = $baseDir;
        $this->title = "SSF &gt; $controller &gt; ".ucfirst($action);
    }

    public function __set($var, $value) {
        $this->vars[$var] = $value;
    }
    
    public function __get($var) {
        return $this->templateHelpers[$var];
    }
    
    public function getController() {
    	return $this->controller;
    }
    
    public function getAction() {
    	return $this->action;
    }

    public function setTitle($title) {
    	$this->title = $title;
    }
    
    public function includeStyle($file, $attributes) {
    	$attributes = $this->buildAttributes($attributes);
    	$this->styles .= "<link type=\"text/css\" rel=\"stylesheet\" href=\"".RELATIVE_URL."/css/$file\" $attributes/>";
    }
    
    public function includeScript($file) {
    	$attributes = $this->buildAttributes($attributes);
    	$this->scripts .= "<script type=\"text/javascript\" src=\"".RELATIVE_URL."/js/$file\" $attributes></script>";
    }    
    
    private function buildAttributes($attributes) {
    	$str = "";
    	foreach($attributes as $key => $value) {
    		$str .= "$key=\"$value\" ";
    	}
    	return $str;
    }
    
    public function registerHelper(TemplateHelper $helper) {
        $helperName = lcfirst($helper->getName());
        $this->templateHelpers[$helperName] = $helper;
    }
    
    public function setLayout($layout) {
    	$this->layout = $layout;
    }

    private function renderBuffer($tpl, $file, $vars, $type="template") {
        if( !file_exists($file) ) {
        	if($type == "template") {
	            throw new TemplateNotFoundException("Template <em>$tpl</em> not found in <em>views/</em>.");
        	}
        	elseif($type == "layout") {
        		throw new LayoutNotFoundException("Layout <em>$tpl</em> not found in <em>views/layouts/</em>.");
        	}        	
        }    	
        extract($vars);
        ob_start();
        include($file);
        return ob_get_clean();
    }

    public function renderPartial($tpl) {
    	$path = $this->baseDir.$tpl.".tpl";
    	return $this->renderBuffer($tpl, $path, $this->vars);
    }    
    
    public function render($tpl) {    	 
        $content = $this->renderPartial($tpl);        
        $tpl2 = $this->layout.".tpl";
        $path = $this->baseDir."layouts".DS.$tpl2;
        $vars = array(
        	"layoutContent" => $content,
        	"layoutTitle" => $this->title,
        	"layoutStyles" => $this->styles,
        	"layoutScripts" => $this->scripts
        );
        $page = $this->renderBuffer($tpl2, $path, $vars, "layout");
        return $page;
    }
}


interface TemplateHelper {
    public function getName();
}


class CoreException extends Exception {
    public function renderHtml() {
    	$exception = get_class($this);
        $template = new Template("Error", $exception, "..".DS."views".DS);
        $template->message = $this->message;
        $template->exceptionClass = get_class($this);
        echo $template->render("core.exception");
    }
}

class ControllerNotFoundException extends CoreException {}
class ActionNotFoundException extends CoreException {}
class TemplateNotFoundException extends CoreException {}
class TemplateHelperNotFoundException extends CoreException {}
class LayoutNotFoundException extends CoreException {}

?>