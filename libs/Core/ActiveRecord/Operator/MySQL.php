<?php

namespace Core\ActiveRecord\Operator;

use \Core\ActiveRecord\Operator;

class MySQL implements Operator {

    /**
     * Boolean AND operator.
     *
     * @static
     * @abstract
     * @param $array
     */
    public static function bAnd() {
        $args = func_get_args();
        $numargs = func_num_args();

        if($numargs == 1) {
            return $args[0];
        }
        elseif($numargs > 1) {
            $str = "";
            for($i = 0; $i < $numargs; ++$i) {
                if($i < $numargs - 1) {
                    $str .= "$args[$i] AND ";
                }
                else {
                    $str .= $args[$i];
                }
            }
            return "($str)";
        }

        return "";
    }


    /**
     * Boolean OR operator.
     *
     * @static
     * @abstract
     * @param $array
     */
    public static function bOr() {
        $args = func_get_args();
        $numargs = func_num_args();

        if($numargs == 1) {
            return $args[0];
        }
        elseif($numargs > 1) {
            $str = "";
            for($i = 0; $i < $numargs; ++$i) {
                if($i < $numargs - 1) {
                    $str .= "$args[$i] OR ";
                }
                else {
                    $str .= $args[$i];
                }
            }
            return "($str)";
        }

        return "";
    }


    /**
     * Boolean NOT operator.
     *
     * @static
     * @abstract
     * @param $array
     */
    public static function bNot($first, $second) {
        return "NOT $first = '$second' ";
    }


    /**
     * Equals to.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function eq($first, $second) {
        return "$first = '$second' ";
    }


    /**
     * Not equals to.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function neq($first, $second) {
        return "$first != '$second'";
    }


    /**
     * Less than.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function lt($first, $second) {
        return "$first < '$second'";
    }


    /**
     * Greater than.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function gt($first, $second) {
        return "$first > '$second'";
    }


    /**
     * Less than or equals to.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function lte($first, $second) {
        return "$first <= '$second'";
    }


    /**
     * Greater than or equals to.
     *
     * @static
     * @abstract
     * @param $first
     * @param $second
     */
    public static function gte($first, $second) {
        return "$first >= '$second'";
    }


    /**
     * In.
     *
     * @static
     * @abstract
     * @param $field
     * @param $array
     */
    public static function in($field, $array) {
        $tmp = implode(", ", $array);
        return "$field IN($tmp)";
    }


    /**
     * Not in.
     *
     * @static
     * @abstract
     * @param $field
     * @param $array
     */
    public static function nin($field, $array) {
        $tmp = implode(", ", $array);
        return "$field NOT IN($tmp)";
    }
}

?>