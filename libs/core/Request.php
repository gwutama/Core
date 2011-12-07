<?php

/**
 * <h1>Class Request</h1>
 * 
 * <p>
 * This class represents HTTP request parameters particularly over GET, POST 
 * and COOKIE methods. The idea behind this class is to wrap PHP global
 * variables $_GET, $_POST and $_COOKIE within an object so it can be
 * sanitized to prevent security attacks.
 * </p>
 * 
 * @example
 * $request = new Request();
 * $foo = $request->get("foo");		// Returns $_GET["foo"]
 * $bar = $request->post("bar");	// Returns $_POST["bar"]
 * 
 * echo $foo->toInt();				// Formats the parameter as integer
 * echo $bar->toString();			// Returns the parameter as string
 * 
 * @see RequestFormat
 * 
 * @author Galuh Utama
 *
 */
class Request {
	/**
	 * Returns parameters passed by GET methods.
	 * 
	 * @param 	mixed $key
	 * @return 	mixed
	 */
	public function get($key) {
		if(isset($_GET[$key])) return new RequestFormat($_GET[$key]);
		return new RequestFormat();
	}

	
	/**
	 * Returns parameters passed by POST methods.
	 *
	 * @param 	mixed $key
	 * @return	mixed
	 */
	public function post($key) {
		if(isset($_POST[$key])) return new RequestFormat($_POST[$key]);
		return new RequestFormat();
	}	

	
	/**
	 * Returns parameters passed by COOKIE  methods.
	 * 
	 * @param	mixed $key
	 * @return	mixed
	 */
	public function cookie($key) {
		if(isset($_COOKIE[$key])) return new RequestFormat($_COOKIE[$key]);
		return new RequestFormat();
	}	
}



/**
 * <h1>Class RequestFormat</h1>
 * 
 * Formats the object returned by Request as primitives.
 * 
 * @see Request
 * 
 * @author Galuh Utama
 *
 */
class RequestFormat {
	/**
	 * Variable value.
	 * 
	 * @var unknown_type
	 */
	private $var;

	
	/**
	 * Constructor sets the variable value.
	 * 
	 * @param unknown_type $var
	 */
	public function __construct($var=null) {
		$this->var = $var;
	}
	
	
	/**
	 * Formats value as integer.
	 * 
	 * @return integer
	 */
	public function toInt() {
		return (int) $this->var;
	}

	
	/**
	 * Formats value as boolean.
	 * 
	 * @return boolean
	 */
	public function toBool() {
		return (bool) $this->var;
	}
	
	
	/**
	 * Formats value as string.
	 * 
	 * @return string
	 */	
	public function toString() {
		if( empty($this->var) ) return null;
		// TODO
		return $this->var;
	}

	
	/**
	 * Magic method to return value as string. Use this with caution.
	 * 
	 * @return string
	 */
	public function __toString() {
		return $this->var;
	}
}

?>