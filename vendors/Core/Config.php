<?php

namespace Core;

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
class Config {

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
            $config = &$config[$tokens[$i]]->children;
        }

        if(@$config[$tokens[$i]] instanceof ConfigNode) {
            $children = $config[$tokens[$i]]->children;
        }
        else {
            $children = array();
        }

        $config[$tokens[$i]] = new ConfigNode($tokens[$i], $value);
        $config[$tokens[$i]]->children = $children;
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

            $value = $config[$tokens[$i]]->value;
            $config = &$config[$tokens[$i]]->children;
        }

        return $value;
    }


    /**
     * Checks whether key contains only alphanumerical characters.
     *
     * @static
     * @param $key  Configuration key. Must be string.
     */
    public static function validKey($key) {
        if(is_string($key) && preg_match("/^[a-zA-Z0-9\.]+$/", $key)) {
            return true;
        }
        return false;
    }

}

?>