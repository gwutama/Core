<?php

namespace Core\ActiveRecord;

interface QueryBuilder {

    /**
     * @abstract
     * @param $table
     * @param array $data
     * @param array $options
     */
    public static function insert($table, $data = array(), $options = array());


    /**
     * @abstract
     * @param $table
     * @param array $options
     */
    public static function select($table, $options = array());


    /**
     * @abstract
     * @param $table
     * @param $data
     * @param array $options
     */
    public static function update($table, $data, $options = array());


    /**
     * @abstract
     * @param $table
     * @param array $options
     */
    public static function delete($table, $options = array());
}

?>
