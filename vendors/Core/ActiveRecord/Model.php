<?php

namespace Core\ActiveRecord;

use \Core\ServiceContainer;
use \Core\ActiveRecordAdapterNotFoundException;

/**
 * <h1>Class Model</h1>
 *
 * <p>
 * This class represents a model part in MVC approach. It uses the Fowler's
 * Active Record pattern, or at least mimics it.
 * </p>
 */
abstract class Model {

    /**
     * Database configuration name from database.yml
     *
     * @var string
     */
    protected $adapter;

    /**
     * Database object. PDO or PDO compliant.
     *
     * @var string
     */
    protected $dbo;

    /**
     * Primary key field
     *
     * @var string
     */
    protected $primaryKey = "id";

    /**
     * 1-1 relationship to other models.
     *
     * @var mixed
     */
    protected $hasOne;

    /**
     * 1-n relationship to other models.
     *
     * @var mixed
     */
    protected $hasMany;

    /**
     * n-1 relationship to other models.
     *
     * @var mixed
     */
    protected $belongsTo;

    /**
     * @todo: Model validations.
     *
     * @var array
     */
    protected $validations = array();

    /**
     * Query values are saved in an array. Can be array of model objects
     * if findAll is called.
     *
     * @var string
     */
    protected $data;


    /**
     * Sets the driver DBO object.
     */
    public function __construct(Adapter $dbo) {
        $this->dbo = $dbo;
        $this->dbo->setModel(get_class($this));
    }


    /**
     * Returns the model object.
     *
     * @static
     * @return string   Model object.
     */
    protected static function instance() {
        $obj = get_class($this);
        return new $obj;
    }


    /**
     * Retrieves result.
     *
     * @static
     * @param $pos      A string either all/first/last/one/primary key id.
     * @param $options  An array of options.
     */
    public static function find($pos, $options = array()) {
        if(is_int($pos)) {
            return self::instance()->findById($pos, $options);
        }
        elseif($pos == "all") {
            return self::instance()->findAll($options);
        }
        elseif($pos == "first") {
            return self::instance()->findFirst($options);
        }
        elseif($pos == "last") {
            return self::instance()->findLast($options);
        }
        elseif($pos == "one") {
            return self::instance()->findOne($options);
        }
    }


    /**
     * Returns all objects in model.
     *
     * @static
     * @param $options  An array of options.
     * @return Model object
     */
    public static function all($options = array()) {
        return self::instance()->findAll($options);
    }


    /**
     * Returns the first object in model.
     *
     * @static
     * @param $options  An array of options.
     * @return Model object
     */
    public static function first($options = array()) {
        return self::instance()->findFirst($options);
    }


    /**
     * Returns the last object in model.
     *
     * @static
     * @param $options  An array of options.
     * @return Model object
     */
    public static function last($options = array()) {
        return self::instance()->findLast($options);
    }


    /**
     * Returns an object in model.
     *
     * @static
     * @param $options  An array of options.
     * @return Model object
     */
    public static function one($options = array()) {
        return self::instance()->findOne($options);
    }


    /**
     * Retrieves an object from model by primary key.
     *
     * @param $pos
     * @param array $options
     */
    public function findById($pos, $options = array()) {
        $this->data = $this->dbo->findById($pos, $options);
        return $this;
    }


    /**
     * Retrieves all objects from model.
     *
     * @param array $options
     */
    public function findAll($options = array()) {
        $this->data = $this->dbo->findAll($options); // returns array of model objects
        return $this;
    }


    /**
     * Retrieves first object from model.
     *
     * @param array $options
     */
    public function findFirst($options = array()) {
        $this->data = $this->dbo->findFirst($options);
        return $this;
    }


    /**
     * Retrieves last object from model.
     *
     * @param array $options
     */
    public function findLast($options = array()) {
        $this->data = $this->dbo->findLast($options);
        return $this;
    }


    /**
     * Retrieves an object from model.
     *
     * @param array $options
     */
    public function findOne($options = array()) {
        $this->data = $this->dbo->findOne($options);
        return $this;
    }


    /**
     * Saves an object.
     */
    public function save() {
        $this->dbo->save($this->data);
    }

    /**
     * Deletes an object.
     */
    public function delete() {
        $this->dbo->delete($this->data);
    }


    /**
     * Magic method to retrieve query result by key.
     *
     * @param $key      Object key.
     */
    public function __get($key) {
        return $this->data[$key];
    }


    /**
     * Magic method to set object member variable by key.
     *
     * @param $key      Object key.
     * @param $value    Object value.
     */
    public function __set($key, $value) {
        $this->data[$key] = $value;
    }
}

?>
