<?php

/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class Request {
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $key
	 */
	public function get($key) {
		if(isset($_GET[$key])) return new RequestFormat($_GET[$key]);
		return new RequestFormat();
	}

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $key
	 */
	public function post($key) {
		if(isset($_POST[$key])) return new RequestFormat($_POST[$key]);
		return new RequestFormat();
	}	

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $key
	 */
	public function cookie($key) {
		if(isset($_COOKIE[$key])) return new RequestFormat($_COOKIE[$key]);
		return new RequestFormat();
	}	
}


/**
 * 
 * Enter description here ...
 * @author Galuh Utama
 *
 */
class RequestFormat {
	/**
	 * 
	 * Enter description here ...
	 * @var unknown_type
	 */
	private $var;

	
	/**
	 * 
	 * Enter description here ...
	 * @param unknown_type $var
	 */
	public function __construct($var=null) {
		$this->var = $var;
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */
	public function toInt() {
		return (int) $this->var;
	}

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function toBool() {
		return (bool) $this->var;
	}
	
	
	/**
	 * 
	 * Enter description here ...
	 */	
	public function toString() {
		if( empty($this->var) ) return null;
		// TODO
		return $this->var;
	}

	
	/**
	 * 
	 * Enter description here ...
	 */
	public function __toString() {
		return $this->var;
	}
}

?>