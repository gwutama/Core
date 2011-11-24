<?php

/**
* <b>Class Template</b>
*
* <p>
* This class represents the "VIEW" part of the MVC approach. It is a fairly
* simple templating system which supports layouts and template helpers.
* Unlike smarty, this uses PHP as a templating language. Which means one
* can use php tags in the template. But that doesn't mean that one should
* mix the application logic with the view/template. This approach is simple,
* fast and requires no template compilation.
* </p>
*
* <p>Usage example:</p>
* <code>
* // initialize template object
* $template = new Template("controllerName", "actionName", "views/");
*
* // set template variables
* $template->someVariable = "foo";
* $template->anotherVariable = "bar";
*
* // set layout title
* $template->setTitle = "Hello, world! This is the page title.";
*
* // render and output the template file views/controllerName.actionName.tpl
* echo $template->render("controllerName.actionName.tpl");
* </code>
*
* @author Galuh Utama
*
*/
class Template {
	/**
	 * Template's base directory, where all template files reside.
	 * $baseDir will be set by the constructor.
	 *
	 * @see Template::__construct()
	 * @var string
	 */
	private $baseDir;

	/**
	 * <p>Template variables. Each key of this array will be passed to the
	 * actual template and can be called as a variable. The array keys
	 * can be set with like this:</p>
	 * <code>
	 * $template = new Template("controller", "action", "views");
	 * $template->someVariable = "foo";
	 * $template->anotherVariale = "bar";
	 * </code>
	 *
	 * <p>Then internally, $vars will be set like this:</p>
	 * <code>
	 * $vars = array("someVariable" => "foo", "anotherVariable" => "bar");
	 * </code>
	 *
	 * <p>then in the actual template, one can use these variables
	 * like this:</p>
	 * <code>
	 * echo $someVariable";
	 * </code>
	 * <p>which will print "foo" out.</p>
	 *
	 * @see Template::__set()
	 * @see Template::__get()
	 * @var array
	 */
	private $vars = array();

	/**
	 * <p>List of template helpers. Template helpers are objects that can be
	 * used in the actual template. Helpers must reside in libs/helpers.
	 * HtmlHelper is loaded by default. </p>
	 *
	 * <p>Example in actual template:</p>
	 * <code>
	 * echo $htmlHelper->image("test.png");
	 * </code>
	 * will output:
	 * <code>
	 * <img src="test.png" />
	 * </code>
	 *
	 * @see Template::registerHelper()
	 * @var array
	 */
	private $templateHelpers = array("HtmlHelper");

	/**
	 * <p>Defines the layout used for this template. A layout is in essence
	 * another template, but it holds the template for certain controllers
	 * and actions. A layout would normally has the opening <html>,
	 * <head> with its stylesheets and javascript includes. And a very basic
	 * <body> tag. See views/layouts/default.tpl for an overview. A layout
	 * should be reside in views/layouts/ directory.</p>
	 *
	 * @see Template::setLayout()
	 * @var string
	 */
	private $layout = "default";

	/**
	 * <p>The layout above can have a page title in the <title></title>
	 * HTML tag. In the layout file one should do this, so they can set
	 * the page title dynamically:</p>
	 * <code>
	 * <title><?php echo $layoutTitle ?></title>
	 * </code>
	 *
	 * @see Template::setTitle()
	 * @var string
	 */
	private $title;

	/**
	 * Each template
	 * @var string
	 */
	private $controller;

	/**
	 *
	 * Enter description here ...
	 * @var string
	 */
	private $action;

	/**
	 *
	 * Enter description here ...
	 * @var string
	 */
	private $styles;

	/**
	 *
	 * Enter description here ...
	 * @var string
	 */
	private $scripts;


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $controller
	 * @param unknown_type $action
	 * @param unknown_type $baseDir
	 */
	public function __construct($controller, $action, $baseDir) {
		$this->controller = $controller;
		$this->action = $action;
		$this->baseDir = $baseDir;
		$this->title = "SSF &gt; $controller &gt; ".ucfirst($action);
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $var
	 * @param unknown_type $value
	 */
	public function __set($var, $value) {
		$this->vars[$var] = $value;
	}


	/**
	 *
	 * Enter description here ...
	 * @return string
	 */
	public function getController() {
		return $this->controller;
	}


	/**
	 *
	 * Enter description here ...
	 */
	public function getAction() {
		return $this->action;
	}


	/**
	 *
	 * Enter description here ...
	 * @return multitype:
	 */
	public function getVars() {
		return $this->vars;
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $file
	 * @param unknown_type $attributes
	 */
	public function includeStyle($file, $attributes = array()) {
		$attributes = $this->buildAttributes($attributes);
		$this->styles .= "<link type=\"text/css\" rel=\"stylesheet\" 
			href=\"".RELATIVE_URL."/css/$file\" $attributes/>\n";
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $file
	 * @param unknown_type $attributes
	 */
	public function includeScript($file, $attributes = array()) {
		$attributes = $this->buildAttributes($attributes);
		$this->scripts .= "<script type=\"text/javascript\" 
			src=\"".RELATIVE_URL."/js/$file\" $attributes></script>\n";
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $attributes
	 * @return string
	 */
	private function buildAttributes($attributes) {
		$str = "";
		foreach((array) $attributes as $key => $value) {
			$str .= "$key=\"$value\" ";
		}
		return $str;
	}


	/**
	 *
	 * Enter description here ...
	 * @param TemplateHelper $helper
	 */
	public function registerHelper(TemplateHelper $helper) {
		$helperName = lcfirst($helper->getName());
		$this->templateHelpers[$helperName] = $helper;
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $var
	 */
	public function __get($var) {
		return $this->templateHelpers[$var];
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $layout
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $tpl
	 * @param unknown_type $file
	 * @param unknown_type $vars
	 * @param unknown_type $type
	 * @throws TemplateNotFoundException
	 * @throws LayoutNotFoundException
	 */
	private function renderBuffer($tpl, $file, $vars, $type="template") {
		if( !file_exists($file) ) {
			if($type == "template") {
				throw new TemplateNotFoundException("Template <em>$tpl.tpl</em>
					 not found in <em>views/</em>.");
			}
			elseif($type == "layout") {
				throw new LayoutNotFoundException("Layout <em>$tpl.tpl</em>
					 not found in <em>views/layouts/</em>.");
			}
		}
		extract($vars);
		ob_start();
		include($file);
		return ob_get_clean();
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $tpl
	 * @return string
	 */
	public function renderPartial($tpl) {
		$path = $this->baseDir.$tpl.".tpl";
		return $this->renderBuffer($tpl, $path, $this->vars);
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $tpl
	 * @return string
	 */
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

?>