<?php

namespace Core\ActiveRecord;

interface Operator {

    /**
     * Boolean AND operator.
     *
     * @static
     * @abstract
     * @param $array
     */
    public static function bAnd();


    /**
     * Boolean OR operator.
     *
     * @static
     * @abstract
     * @param $array
     */
    public static function bOr();


    /**
     * Boolean NOT operator.
     *
     * @static
     * @abstract
     * @param $array
     */
    public static function bNot($first, $second);


    /**
     * Equals to.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function eq($first, $second);


    /**
     * Not equals to.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function neq($first, $second);


    /**
     * Less than.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function lt($first, $second);


    /**
     * Greater than.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function gt($first, $second);


    /**
     * Less than or equals to.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function lte($first, $second);


    /**
     * Greater than or equals to.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function gte($first, $second);


    /**
     * In.
     *
     * @static
     * @abstract
     * @param $field
     * @param $array
     */
    public static function in($field, $array);


    /**
     * Not in.
     *
     * @static
     * @abstract
     * @param $field
     * @param $array
     */
    public static function nin($field, $array);
}

?>
