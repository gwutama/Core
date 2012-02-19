<?php

namespace Core\ActiveRecord;
use \Core\Inflector;

abstract class QueryBuilder {

    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $tableName;


    /**
     * @param $model
     */
    public function __construct($model) {
        // Pluralize model name
        // Due to the nature of namespace and get_class() function,
        // get_class() function returns for example Models\Modelname
        // Replace Core\ActiveRecord\ with nothing.
        $this->model = str_replace("Models\\", "", $model);
        $this->tableName = Inflector::tableize($this->model);
    }


    /**
     * @abstract
     * @param array $data
     * @param array $options
     */
    abstract public function insert($data = array(), $options = array());


    /**
     * @abstract
     * @param array $options
     */
    abstract public function select($options = array());


    /**
     * @abstract
     * @param $data
     * @param array $options
     */
    abstract public function update($data, $options = array());


    /**
     * @abstract
     * @param array $options
     */
    abstract public function delete($options = array());
}

?>
