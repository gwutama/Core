<?php

namespace Core\Storage;

abstract class Storage implements Storageable {

    /**
     * Checks whether key contains only alphanumerical characters.
     *
     * @static
     * @param $key  Storage key. Must be string.
     * @return bool
     */
    public static function validKey($key) {
        if(is_string($key) && preg_match("/^[a-zA-Z0-9\.]+$/", $key)) {
            return true;
        }
        return false;
    }


    /**
     * Sets the Storage based on an array with keys.
     *
     * @static
     * @param $array
     * @param bool $setArrayAsArray
     * @param string $parent
     */
    public static function setArray($array, $setArrayAsArray = false, $parent = "") {
        foreach((array) $array as $key=>$value) {
            if( !is_array($value) ) {
                if($parent) {
                    $key = "$parent.$key";
                }
                static::set($key, $value);
            }
            else {
                if($setArrayAsArray) {
                    static::set($key, $value);
                }
                else {
                    $tmp = $parent;
                    if($parent) {
                        $parent .= ".$key";
                    }
                    else {
                        $parent = $key;
                    }
                    static::setArray($value, $setArrayAsArray, $parent);
                    $parent = $tmp;
                }
            }
        }
    }

}

?>