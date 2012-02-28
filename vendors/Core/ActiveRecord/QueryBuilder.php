<?php

namespace Core\ActiveRecord;

interface QueryBuilder {

    /**
     * @abstract
     * @param array $data
     * @param array $options
     */
    public static function insert($table, $data = array(), $options = array());


    /**
     * @abstract
     * @param array $options
     */
    public static function select($table, $options = array());


    /**
     * @abstract
     * @param $data
     * @param array $options
     */
    public static function update($table, $data, $options = array());


    /**
     * @abstract
     * @param array $options
     */
    public static function delete($table, $options = array());
}

?>
