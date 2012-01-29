<?php

/**
 * <h1>Class Autoloader</h1>
 *
 * <p>
 * A simple autoloader class.
 * </p>
 */
class Autoloader {

    /**
     * An array of directories.
     * @var array
     */
    private $dirs = array();


    /**
     * Registers the autoloader method.
     */
    public function __construct() {
        spl_autoload_register(array($this, "loader"));
    }


    /**
     * Registers a directory.
     *
     * @param $dir  Path to directory.
     */
    public function register($dir) {
        $this->dirs[] = $dir;
    }


    /**
     * The actual autoloader method.
     *
     * @param $class    The class name
     */
    public function loader($class) {
        // Zend style: treat underscores as slashes (subdirectories).
        // Because php 5.2 is still everywhere.
        $class = str_replace("_", "/", $class);
        foreach($this->dirs as $dir) {
            // Finally include the file.
            include_once $dir."/".$class.".php";
        }
    }
}

?>