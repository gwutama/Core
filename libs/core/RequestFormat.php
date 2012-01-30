<?php

namespace Core;

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