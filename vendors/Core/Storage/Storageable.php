<?php

namespace Core\Storage;

interface Storageable {
    /**
     * Sets the Storage based on key.
     * Key could be in format parent.child.child etc.
     * Thus a key can have unlimited sub keys.
     *
     * @static
     * @param $key      Storage key
     * @param $value    Storage value
     */
    public static function set($key, $value);


    /**
     * Returns the storage based on key.
     * Key could be in format parent.child.child etc.
     * Thus a key can have unlimited sub keys.
     *
     * @static
     * @param $key  Storage key
     * @return mixed
     */
    public static function get($key);


    /**
     * Clears storage.
     *
     * @static
     */
    public static function clear();
}

?>