<?php

namespace Core\ActiveRecord\Operator;

use \Core\ActiveRecord\Operator;

class MySQL implements Operator {

    /**
     * The binds array contains binds values
     * from the operator methods in this class.
     *
     * @var array
     */
    protected static $binds = array();


    /**
     * Get the binds array.
     *
     * @static
     * @return array
     */
    public static function getBinds() {
        return self::$binds;
    }


    public static function setBind($key, $value) {
        if($key) {
            self::$binds[":$key"] = $value;
        }
        else {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }
    }


    /**
     * Sets values of an array to the bind variable.
     *
     * @static
     * @param $data Array
     */
    public static function setBinds($data) {
        foreach((array)$data as $key=>$value) {
            if($key) {
                self::$binds[":$key"] = $value;
            }
            else {
                throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
            }
        }
    }


    /**
     * Resets the binds array.
     *
     * @static
     */
    public static function clearBinds() {
        self::$binds = array();
    }


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
        if(!$first) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

        self::$binds[":$first"] = $second;
        return "NOT `$first` = :$first";
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
        if(!$first) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

        self::$binds[":$first"] = $second;
        return "`$first` = :$first";
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
        if(!$first) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

        self::$binds[":$first"] = $second;
        return "`$first` != :$first";
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
        if(!$first) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

        self::$binds[":$first"] = $second;
        return "`$first` < :$first";
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
        if(!$first) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

        self::$binds[":$first"] = $second;
        return "`$first` > :$first";
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
        if(!$first) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

        self::$binds[":$first"] = $second;
        return "`$first` <= :$first";
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
        if(!$first) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

        self::$binds[":$first"] = $second;
        return "`$first` >= :$first";
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
        if(!$field) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

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
        if(!$field) {
            throw new \Core\ActiveRecordOperatorException("Operator key cannot be empty.");
        }

        $tmp = implode(", ", $array);
        return "$field NOT IN($tmp)";
    }

}

?>