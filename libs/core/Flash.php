<?php

/**
 * <h1>Class Flash</h1>
 *
 * <p>
 * This class represents flash messages, which are saved
 * in the global $_SESSION variable.
 * </p>
 *
 * @example
 * TODO
 */
class Flash {
	/**
	 * Error flash messages.
	 */
	private $error = array();
	
	/**
	 * Warning flash messages.
	 */
	private $warning = array();
	
	/**
	 * Success flash messages.
	 */
	private $success = array();
	
	/**
	 * Info flash messages.
	 */
	private $info = array();


	/**
	 * The constructor starts session if the session has not
	 * been initialized then appends contents of flash messages
	 * into session variable, if any.
	 */
	public function __construct() {
		if(!isset($_SESSION)) {
			session_start();
		}

		$this->error = $_SESSION["flash"][FlashType::ERROR];
		$this->warning = $_SESSION["flash"][FlashType::WARNING];
		$this->success = $_SESSION["flash"][FlashType::SUCCESS];
		$this->info = $_SESSION["flash"][FlashType::INFO];
	}


	/**
	 * This method clears flash messages from session variable.
	 *
	 * @param $type	Flash message type, instance of FlashType.
	 */
	public static function clearFromSession(FlashType $type) {
		unset($_SESSION["flash"][$type]);
	}    


	/**
	 * Copies flash messages from this object to the 
	 * golab session variable.
	 */
	private function copyToSession() {
		$_SESSION["flash"][FlashType::ERROR] = $this->error;
		$_SESSION["flash"][FlashType::WARNING] = $this->warning;
		$_SESSION["flash"][FlashType::SUCCESS] = $this->success;
		$_SESSION["flash"][FlashType::INFO] = $this->info;        
	}


	/**
	 * Appends a flash message into a certain flash container.
	 *
	 * @param $msg	Message, should be from type string.
	 * @param $type	Flash type. Should be from type FlashType.
	 */
	public function append($msg, FlashType $type) {
		switch($type) {
			case FlashType::ERROR :
				$this->error[] = $msg;
				break;
			case FlashType::WARNING :
				$this->warning[] = $msg;
				break;
			case FlashType::SUCCESS :
				$this->success[] = $msg;
				break;
			case FlashType::INFO :
				$this->info[] = $msg;
				break;
		}

		$this->copyToSession();
	}


	/**
	 * Returns all contents of a certain flash type.
	 * After calling this method, flash messages of this type
	 * will be automatically erased.
	 *
	 * @param $type	Flash message type. Instance of FlashType.
	 * @return Array of messages.
	 */
	public static function get(FlashType $type) {
		$flash = $_SESSION["flash"][$type];
		self::clearFromSession($type);

		if(!is_array($flash)) {
			return (array) $flash;
		}
		else {        
			return $flash;
		}
	}
}



/**
 * <h1>Class FlashType</h1>
 *
 * <p>
 * Struct for flash object. This class represets types
 * of flash messages, which are saved in the $_SESSION
 * global variable.
 * </p>
 *
 */
class FlashType {
	const ERROR = 4;
	const WARNING = 3;
	const SUCCESS = 2;
	const INFO = 1;
}

?>
