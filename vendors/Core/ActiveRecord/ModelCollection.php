<?php

namespace Core\ActiveRecord;
use Core\ActiveRecord\Operator\MySQL as Op;
use Core\Utility\ObjectCollection;

class ModelCollection extends ObjectCollection {

    /**
     * Database Object
     *
     * @var Adapter
     */
    private $dbo;

    /**
     * Primary key of all models. Defaults to id.
     *
     * @var string
     */
    private $primaryKey;

    /**
     * Model name.
     *
     * @var string
     */
    private $modelName;

    /**
     * The constructor sets services if an array of models
     * is passed to the constructor.
     * @param $modelName
     * @param \Core\ActiveRecord\Adapter $dbo
     * @param string $primaryKey
     * @param array $models
     */
    public function __construct($modelName, Adapter &$dbo, $primaryKey = "id", $models = array()) {
        $this->modelName = $modelName;
        $this->dbo = $dbo;
        $this->primaryKey = $primaryKey;
        $this->objects = $models;
    }


    /**
     * Returns the model name.
     * @return string
     */
    public function getName() {
        return $this->modelName;
    }


    /**
     * Sets the primary key of all models.
     *
     * @param $key
     */
    public function setPrimaryKey($key) {
        $this->primaryKey = $key;
    }


    /**
     * Gets the primary key for all models.
     *
     * @internal param $key
     * @return string
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }


    /**
     * Returns the n-th element.
     *
     * @param $key
     * @return mixed
     */
    public function get($key) {
        return @$this->objects[$key];
    }


    /**
     * Returns primary key values of all models.
     * @return array
     */
    public function getPrimaryKeyValues() {
        $tmp = array();
        foreach($this->objects as $model) {
            $tmp[] = $model->{$this->primaryKey};
        }
        return $tmp;
    }


    /**
     * Appends a model into collection.
     *
     * @param Model $model
     */
    public function append(Model &$model) {
        $this->objects[] = $model;
    }


    /**
     * Deletes collection.
     * @param array $options
     */
    public function delete($options = array()) {
        $this->dbo->deleteAll($this, $options);
    }


    /**
     * Updates collection.
     * @param $data
     * @param array $options
     */
    public function save($data, $options = array()) {
        $this->dbo->updateAll($this, $data, $options);
    }

}

?>