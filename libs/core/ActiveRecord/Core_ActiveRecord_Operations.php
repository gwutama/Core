<?php

abstract class Core_ActiveRecord_Operations {

    /**
     * @static
     * @abstract
     * @param $array
     */
    abstract public static function boolAnd($array);


    /**
     * @static
     * @abstract
     * @param $array
     */
    abstract public static function boolOr($array);


    /**
     * @static
     * @abstract
     * @param $array
     */
    abstract public static function boolNot($array);


    /**
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    abstract public static function equals($first, $second);


    /**
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    abstract public static function notEquals($first, $second);


    /**
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    abstract public static function less($first, $second);


    /**
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    abstract public static function greater($first, $second);


    /**
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    abstract public static function lessEqual($first, $second);


    /**
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    abstract public static function greaterEqual($first, $second);
}

?>
