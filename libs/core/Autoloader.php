<?php

namespace Core;

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
        foreach($this->dirs as $dir) {
            $file = $dir . "/" . str_replace("\\", "/", $class) . ".php";
            var_dump($file);
            if(file_exists($file)) {
                include_once $file;
            }
            else {
                throw new FileNotFoundException("File not found: ". $file);
            }
        }
    }
}

?>