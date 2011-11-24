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
* @example
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
	 * Each template refers to a controller. This is set in constructor.
	 * 
	 * @see Template::__construct()
	 * @var string
	 */
	private $controller;

	/**
	 * Each template refers to an action, which is a part of a controller.
	 * This is set in constructor.
	 * 
	 * @see Template::__construct()
	 * @var string
	 */
	private $action;

	/**
	 * <p>Contains html tags to include stylesheets to be used in the layout.
	 * In the actual template file, one can do this to include specific
	 * stylesheet for a specific action:</p>
	 * <code>
	 * $this->includeStyle("somestyle.css");
	 * </code>
	 * 
	 * <p>This will be translated into</p>
	 * <code>
	 * <link type="text/css" rel="stylsheet" href="/css/somestyle.css" />
	 * </code>
	 * <p>in the layout file.</p>
	 * 
	 * @see Template::includeStyle()
	 * @var string
	 */
	private $styles;

	/**
	 * <p>Contains html tags to include javascripts to be used in the layout.
	 * In the actual template file, one can do this to include specific
	 * javascript for a specific action:</p>
	 * <code>
	 * $this->includeScript("somescript.js");
	 * </code>
	 * 
	 * <p>This will be translated into</p>
	 * <code>
	 * <script type="text/javascript" src="/js/somescript.js" />
	 * </code>
	 * <p>in the layout file.</p>
	 * 
	 * @see Template::includeScript()
	 * @var string
	 */
	private $scripts;

	
	/**
	 * The constructor sets the controller name, action name
	 * and the base directory of the views. By default, it sets the
	 * page title to "SSF > {controller_name} > {action_name}". 
	 * This however can be omitted by setting the layout title with setTitle().
	 * 
	 * @see 	Template::setTitle()
	 * @param 	string 		$controller		The controller name.
	 * @param 	string 		$action			The action name.
	 * @param 	string 		$baseDir		The base directory for views.
	 */
	public function __construct($controller, $action, $baseDir) {
		$this->controller = $controller;
		$this->action = $action;
		$this->baseDir = $baseDir;
		$this->title = "SSF &gt; $controller &gt; ".ucfirst($action);
	}


	/**
	 * Magic methods to set the template variables, which can be called
	 * in the template file using $variableName.
	 * 
	 * @see		Template::vars
	 * @param 	string 		$var		Variable name.
	 * @param 	string 		$value		The value.
	 */
	public function __set($var, $value) {
		$this->vars[$var] = $value;
	}


	/**
	 * Returns the controller name.
	 * 
	 * @return 	string
	 */
	public function getController() {
		return $this->controller;
	}


	/**
	 * Returns the action name.
	 * 
	 * @return	string
	 */
	public function getAction() {
		return $this->action;
	}


	/**
	 * Returns the template variables.
	 * 
	 * @return 	array:
	 */
	public function getVars() {
		return $this->vars;
	}


	/**
	 * Sets the layout title.
	 * 
	 * @param	String		$title		The page title.
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * Includes a stylesheet from the webroot/css/ directory. This method
	 * should be called in the template file. Extra attributes is by default
	 * empty.
	 * 
	 * @example
	 * <code>
	 * $this->includeStyle("somestyle.css", array("media" => "all"));
	 * </code>
	 * 
	 * @see		Template::styles
	 * @param	string 		$file		The style file name.
	 * @param	array		$attributes	Extra attributes.
	 */
	public function includeStyle($file, $attributes = array()) {
		$attributes = $this->buildAttributes($attributes);
		$this->styles .= "<link type=\"text/css\" rel=\"stylesheet\" 
			href=\"".RELATIVE_URL."/css/$file\" $attributes/>\n";
	}


	/**
	 * Includes a javascript from the webroot/js/ directory. This method
	 * should be called in the template file. Extra attributes is by default 
	 * empty.
	 * 
	 * @example
	 * <code>
	 * $this->includeStyle("somescript.js", array("encoding" => "UTF-8"));
	 * </code>
	 * 
	 * @see		Template::scripts
	 * @param	string 		$file		The script file name.
	 * @param	array		$attributes	Extra attributes.
	 */
	public function includeScript($file, $attributes = array()) {
		$attributes = $this->buildAttributes($attributes);
		$this->scripts .= "<script type=\"text/javascript\" 
			src=\"".RELATIVE_URL."/js/$file\" $attributes></script>\n";
	}


	/**
	 * A private method to build attributes from an array using the
	 * key and value data.
	 *
	 * @param	array		$attributes	The attributes.
	 * @return 	string
	 */
	private function buildAttributes($attributes) {
		$str = "";
		foreach((array) $attributes as $key => $value) {
			$str .= "$key=\"$value\" ";
		}
		return $str;
	}


	/**
	 * Registers a template helper into the template object, so it can be 
	 * used/called in the template file.
	 * 
	 * @see		Template::templateHelpers
	 * @param 	TemplateHelper	$helper		The template helper.
	 */
	public function registerHelper(TemplateHelper $helper) {
		$helperName = lcfirst($helper->getName());
		$this->templateHelpers[$helperName] = $helper;
	}


	/**
	 * Magic method, which returns the template variables.
	 * 
	 * @see		Template::vars
	 * @param	mixed		$var		Anything.
	 * @return	mixed
	 */
	public function __get($var) {
		return $this->templateHelpers[$var];
	}


	/**
	 * Sets the template's layout name. Layout should be exists under
	 * /views/layouts/ directory.
	 * 
	 * @see		Template::layout
	 * @param	string		$layout		Layout name.
	 */
	public function setLayout($layout) {
		$this->layout = $layout;
	}


	/**
	 * A private method to render a template file.
	 * 
	 * @param	string	$tpl	Template name.
	 * @param	string	$file	Template file.
	 * @param	array	$vars	Variables to be parsed.
	 * @param	string	$type	Template types. Either "template" or "layout".
	 * @throws 	TemplateNotFoundException
	 * @throws 	LayoutNotFoundException
	 * @retun	string
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
	 * Renders a template within a template.
	 *
	 * @example
	 * In the template file:
	 * <code>
	 * echo $this->renderPartial("sidebar.tpl");
	 * </code>
	 * This will render sidebar.tpl into the current template. Very useful
	 * to include template parts and sections.
	 * 
	 * @see		Template::renderBuffer()
	 * @see		Template::render()
	 * @param	string	$tpl	Template name.
	 * @return 	string
	 */
	public function renderPartial($tpl) {
		$path = $this->baseDir.$tpl.".tpl";
		return $this->renderBuffer($tpl, $path, $this->vars);
	}


	/**
	 * Renders the whole view, including the layout and templates in that
	 * layout. A layout consists of $layoutContent, $layoutTitle, $layoutStyles
	 * and $layoutScripts. These should be echoed in the layout file.
	 * See views/layouts/default.tpl for an example.
	 * 
	 * @example
	 * In a controller file:
	 * <code>
	 * $template = new Template("ControllerName", "ActionName", "views/");
	 * echo $template->render("controllerName.actionName.tpl");
	 * </code>
	 * 
	 * @see		Template::renderBuffer()
	 * @see		Template::renderPartial()
	 * @param	string	$tpl	Template name.
	 * @return 	string
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