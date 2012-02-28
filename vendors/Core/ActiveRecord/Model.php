<?php

namespace Core\ActiveRecord;

use \Core\ServiceContainer;
use \Core\Storage\Config;
use \Core\ActiveRecordAdapterNotFoundException;
use \Core\ActiveRecordModelNoAdapterSetException;
use \Core\ActiveRecordModelValidationException;
use \Core\Inflector;

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
     * Whether data has been fetched or not.
     *
     * @var bool
     */
    protected $fetched = false;

    /**
     * Query values are saved in an array. Can be array of model objects
     * if findAll is called.
     *
     * @var string
     */
    protected $data;

    /**
     * Database object. PDO or PDO compliant.
     */
    protected static $dbo;

    /**
     * Primary key field
     *
     * @var string
     */
    public static $primaryKey = "id";

    /**
     * 1-1 relationship to other models.
     *
     * @var mixed
     */
    public static $hasOne;

    /**
     * 1-n relationship to other models.
     *
     * @var mixed
     */
    public static $hasMany;

    /**
     * n-1 relationship to other models.
     *
     * @var mixed
     */
    public static $belongsTo;

    /**
     * Model profile. Defaults to "default".
     *
     * @var string
     */
    public static $databaseProfile = "default";

    /**
     * Valid fields for this model.
     *
     * @var array
     */
    public static $fields;


    /**
     * Tablename for this model.
     *
     * @var
     */
    public static $tableName;


    /**
     * Sets the driver DBO object.
     */
    public function __construct($fetched = false) {
        $this->fetched = $fetched;

        // Set adapter to dbo
        if(static::$dbo == null) {
            $adapters = new AdapterServiceContainer();
            static::$dbo = $adapters->getService(static::$databaseProfile);
        }
    }


    /**
     * Returns current model's primary key.
     *
     * @return string
     */
    public function getPrimaryKey() {
        return static::$primaryKey;
    }


    /**
     * Returns all data.
     *
     * @return string
     */
    public function getData() {
        return $this->data;
    }


    /**
     * Returns the model name (without namespace).
     */
    public function getName() {
        $class = get_class($this);
        $class = str_replace("Models\\", "", $class);
        $class = str_replace("\\", "", $class);
        return $class;
    }


    /**
     * Retrieves an object from model by primary key.
     *
     * @param $pos
     */
    public function findById($pos, $options = array()) {
        if(static::$dbo) {
            return static::$dbo->findById($this->getName(), $pos, $options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Retrieves all objects from model.
     *
     * @param array $options
     */
    public function findAll($options = array()) {
        if(static::$dbo) {
            return static::$dbo->findAll($this->getName(), $options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Retrieves first object from model.
     *
     * @param array $options
     */
    public function findFirst($options = array()) {
        if(static::$dbo) {
            return static::$dbo->findFirst($this->getName(), $options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Retrieves last object from model.
     *
     * @param array $options
     */
    public function findLast($options = array()) {
        if(static::$dbo) {
            return static::$dbo->findLast($this->getName(), $options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Retrieves an object from model.
     *
     * @param array $options
     */
    public function findOne($options = array()) {
        if(static::$dbo) {
            return static::$dbo->findOne($this->getName(), $options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Saves objects.
     */
    public function save($options = array()) {
        if(static::$dbo) {
            if($this->fetched == false) {
                $pkValue = static::$dbo->create($this->getName(), $this->data, $options);
                $this->__set(static::$primaryKey, $pkValue);
            }
            else {
                static::$dbo->update($this, $this->data, $options);
            }
        }
        else {
            throw new ActiveRecordModelNoAdapterSetException();
        }
    }

    /**
     * Deletes objects.
     */
    public function delete($options = array()) {
        if(static::$dbo) {
            static::$dbo->delete($this, $options);
        }
        else {
            throw new ActiveRecordModelNoAdapterSetException();
        }
    }


    /**
     * Executes an adapter specific statement.
     *
     * @param $statement
     */
    public function execute($statement) {
        if(static::$dbo) {
            static::$dbo->execute($statement);
        }
        else {
            throw new ActiveRecordModelNoAdapterSetException();
        }
    }


    /**
     * Queries an adapter specific statement.
     *
     * @param $statement
     * @return mixed
     */
    public function query($statement) {
        if(static::$dbo) {
            return static::$dbo->query($statement);
        }
        else {
            throw new ActiveRecordModelNoAdapterSetException();
        }
    }


    /**
     * Magic method to retrieve query result by key.
     *
     * @param $key      Object key.
     */
    public function __get($key) {
        $key = Inflector::underscore($key);
        return @$this->data[$key];
    }


    /**
     * Magic method to set object member variable by key.
     *
     * @param $key      Object key.
     * @param $value    Object value.
     */
    public function __set($key, $value) {
        $key = Inflector::underscore($key);
        $this->data[$key] = $value;
    }


    /**
     * Validates all constraints. Throws ActiveRecordModelValidationException
     * on failure.
     */
    public function validateAll() {
        foreach((array) $this->data as $key=>$value) {
            // Try to call validation method if it exists.
            // A validation method will return an exception
            // if it fails.
            $method = "validate".ucfirst($key);
            if(method_exists($this, $method)) {
                $this->{$method}($value);
            }
        }
    }


    /**
     * Validates all constraints and returns error messages.
     */
    public function getValidationErrors() {
        $errors = array();

        foreach((array) $this->data as $key=>$value) {
            try {
                // Try to call validation method if it exists.
                // A validation method will return an exception
                // if it fails.
                $method = "validate".ucfirst($key);
                if(method_exists($this, $method)) {
                    $this->{$method}($value);
                }
            }
            catch(ActiveRecordModelValidationException $e) {
                $errors = $e->getMessage();
            }
        }

        return $errors;
    }
}

?>