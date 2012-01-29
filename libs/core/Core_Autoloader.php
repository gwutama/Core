<?php

/**
 * <h1>Class Core_Autoloader</h1>
 *
 * <p>
 * A simple autoloader class.
 * </p>
 */
class Core_Autoloader {

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
        $path = explode("_", $class);

        $path[count($path)-1] = $class;
        $path = implode("/", $path);

        foreach($this->dirs as $dir) {
            // Finally include the file.
            $file = $dir . "/" . $path . ".php";
            if(file_exists($file)) {
                include_once $file;
            }
        }
    }
}

?>