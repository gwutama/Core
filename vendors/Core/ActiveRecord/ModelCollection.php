<?php

namespace Core\ActiveRecord;
use Core\ActiveRecord\Operator\MySQL as Op;

class ModelCollection implements \Iterator {

    /**
     * Database Object
     *
     * @var Adapter
     */
    private $dbo;

    /**
     * @var array
     */
    private $models = array();

    /**
     * Primary key of all models. Defaults to id.
     *
     * @var string
     */
    private $primaryKey;


    /**
     * The constructor sets services if an array of models
     * is passed to the constructor.
     */
    public function __construct(Adapter &$dbo, $primaryKey = "id", $models = array()) {
        $this->dbo = $dbo;
        $this->primaryKey = $primaryKey;
        $this->models = $models;
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
     * @param $key
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
        return @$this->models[$key];
    }


    /**
     * Returns primary key values of all models.
     */
    public function getPkValues() {
        $tmp = array();
        foreach($this->models as $model) {
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
        $this->models[] = $model;
    }


    /**
     * Deletes collection.
     */
    public function delete() {
        $in = $this->getPkValues();
        $this->dbo->delete(array(
            "conditions" => Op::in($this->primaryKey, $in)
        ));
    }


    /**
     * Updates collection.
     */
    public function update($data = array()) {
        $in = $this->getPkValues();
        $this->dbo->update($data, array(
            "conditions" => Op::in($this->primaryKey, $in)
        ));
    }


    /**
     * Iterator rewind().
     */
    public function rewind() {
        reset($this->models);
    }


    /**
     * Iterator current().
     *
     * @return mixed
     */
    public function current() {
        return current($this->models);
    }


    /**
     * Iterator next().
     *
     * @return mixed
     */
    public function next() {
        return next($this->models);
    }


    /**
     * Iterator key().
     *
     * @return mixed
     */
    public function key() {
        return key($this->models);
    }


    /**
     * Iterator valid().
     *
     * @return bool
     */
    public function valid() {
        $key = key($this->models);
        return ($key !== null && $key !== false);
    }


    /**
     * Returns the number of models.
     *
     * @return int
     */
    public function count() {
        return count($this->models);
    }
}

?>