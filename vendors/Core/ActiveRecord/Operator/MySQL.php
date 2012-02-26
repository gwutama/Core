<?php

namespace Core\ActiveRecord\Operator;

use \Core\ActiveRecord\Operatorable;

class MySQL implements Operatorable {

    /**
     * The binds array contains binds values
     * from the operator methods in this class.
     *
     * @var array
     */
    protected static $binds = array();


    public function __call($name, $arguments) {
        $class = get_class($this);
        return call_user_func_array(array($class, $name), $arguments);
    }


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
            $key = str_replace(".", "_", $key);
            self::$binds[":$key"] = $value;
            return $key;
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

        self::setBind($first, $second);
        $key = preg_replace("/([\w0-9_]+)/", "`$1`", $first);
        return "NOT $key = :$first";
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
        $bind = self::setBind($first, $second);
        $key = preg_replace("/([\w0-9_]+)/", "`$1`", $first);
        return "$key = :$bind";
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
        $bind = self::setBind($first, $second);
        $key = preg_replace("/([\w0-9_]+)/", "`$1`", $first);
        return "$key != :$bind";
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
        $bind = self::setBind($first, $second);
        $key = preg_replace("/([\w0-9_]+)/", "`$1`", $first);
        return "$key < :$bind";
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
        $bind = self::setBind($first, $second);
        $key = preg_replace("/([\w0-9_]+)/", "`$1`", $first);
        return "$key > :$bind";
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
        $bind = self::setBind($first, $second);
        $key = preg_replace("/([\w0-9_]+)/", "`$1`", $first);
        return "$key <= :$bind";
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
        $bind = self::setBind($first, $second);
        $key = preg_replace("/([\w0-9_]+)/", "`$1`", $first);
        return "$key >= :$bind";
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
        $tmp = preg_replace("/([\w0-9_]+)/", "'$1'", $tmp);
        return "`$field` IN($tmp)";
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
        $tmp = preg_replace("/([\w0-9_]+)/", "'$1'", $tmp);
        return "`$field` NOT IN($tmp)";
    }

}

?>