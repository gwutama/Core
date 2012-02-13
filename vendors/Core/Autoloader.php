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
     * Returns an array of registered directories.
     *
     * @return array
     */
    public function getDirs() {
        return $this->dirs;
    }


    /**
     * The actual autoloader method.
     *
     * @param $class    The class name
     */
    public function loader($class) {
        $file = str_replace("\\", "/", $class) . ".php";
        foreach($this->dirs as $dir) {
            $path = $dir . $file;
            if(file_exists($path)) {
                include_once $path;
                return;
            }
        }
        throw new FileNotFoundException("File not found: " . $file);
    }
}

?>