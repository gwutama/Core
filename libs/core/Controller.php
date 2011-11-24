<?php

/**
* Class Controller
* Enter description here ...
* @author Galuh Utama
*
*/
class Controller {
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $template;
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $name;
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $action;
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $templateHelpers = array();
	
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	protected $output = "html";

	
	/**
	 * Builds the
	 *
	 * @param unknown_type $name
	 * @param unknown_type $action
	 * @throws TemplateHelperNotFoundException
	 */
	public function __construct($name, $action) {
		$this->name = $name;
		$this->action = $action;
		try {
			$this->template = new Template($name, $action, "..".DS."views".DS);
		}
		catch(CoreException $e) {
			$e->render();
		}
		foreach( (array) $this->templateHelpers as $helper) {
			$file = "..".DS."libs".DS."helpers".DS.$helper.".php";
			if( !file_exists($file) ) {
				throw new TemplateHelperNotFoundException("Template helper
                	<em>$helper</em> not found in <em>libs/</em>.");        
			}
			include($file);
			$obj = new $helper();
			$this->template->registerHelper($obj);
		}
	}


	/**
	 * Enter description here ...
	 */
	public function index() {
		 
	}


	/**
	 *
	 * @param unknown_type $var
	 * @param unknown_type $value
	 */
	public function set($var, $value) {
		$this->template->$var = $value;
	}


	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $var
	 */
	public function get($var) {
		return $this->template->$var;
	}

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $output
	 */
	public function setOutput($output) {
		$this->output = $output;
	}

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function renderTemplate() {
		if($this->output == "html") {
			$tpl = strtolower($this->name.".".$this->action);
			echo $this->template->render($tpl);
		}
		elseif($this->output == "json") {
			echo json_encode($this->template->getVars());
		}
	}
}


?>