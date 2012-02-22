<?php

namespace Core\Storage;
use Core\InvalidConfigKeyException;

/**
 * <h1>Class Config</h1>
 *
 * <p>
 * This class represents a configuration class.
 * </p>
 *
 * @example
 * Config::set("foo", "bar");
 * Config::set("foo.acme", "blah");
 *
 * var_dump(Config::get("foo")); // returns bar
 * var_dump(Config::get("foo.acme")); // returns blah
 */
class Config extends Storage {

    /**
     * Configurations are statically saved in an array.
     */
    public static $configs = array();


    /**
     * Sets the configuration based on key.
     * Key could be in format parent.child.child etc.
     * Thus a key can have unlimited sub keys.
     *
     * @static
     * @param $key      Configuration key
     * @param $value    Configuration value
     */
    public static function set($key, $value) {
        // throw exception if key contains non alphanumeric characters
        if(self::validKey($key) == false) {
            throw new InvalidConfigKeyException($key);
        }

        $config =& self::$configs;

        $tokens = explode(".", $key);
        $count = count($tokens);
        for($i = 0; $i < $count-1; $i++) {
            if(@$config[$tokens[$i]] instanceof ConfigNode == false) {
                $config[$tokens[$i]] = new ConfigNode($tokens[$i]);
            }
            $config = &$config[$tokens[$i]]->getChildren();
        }

        if(@$config[$tokens[$i]] instanceof ConfigNode) {
            $children = $config[$tokens[$i]]->getChildren();
        }
        else {
            $children = array();
        }

        $config[$tokens[$i]] = new ConfigNode($tokens[$i], $value);
        $config[$tokens[$i]]->setChildren($children);
    }


    /**
     * Returns the configuration based on key.
     * Key could be in format parent.child.child etc.
     * Thus a key can have unlimited sub keys.
     *
     * @static
     * @param $key  Configuration key
     * @return mixed
     */
    public static function get($key) {
        // throw exception if key contains non alphanumeric characters
        if(self::validKey($key) == false) {
            throw new InvalidConfigKeyException($key);
        }

        $config =& self::$configs;

        $tokens = explode(".", $key);
        $count = count($tokens);
        $value = null;

        for($i = 0; $i < $count; $i++) {
            if( array_key_exists($tokens[$i], $config) == false ){
                return null;
            }

            $value = $config[$tokens[$i]]->getValue();
            $config = &$config[$tokens[$i]]->getChildren();
        }

        if($value || count($config) == 0) {
            return $value;
        }
        return $config;
    }


    /**
     * Clears configuration.
     *
     * @static
     */
    public static function clear() {
        self::$configs = array();
    }
}

?>