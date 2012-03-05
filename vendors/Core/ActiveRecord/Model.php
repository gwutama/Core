<?php

namespace Core\ActiveRecord;

use \Core\ServiceContainer;
use \Core\Storage\Config;
use \Core\ActiveRecordAdapterNotFoundException;
use \Core\ActiveRecordModelNoAdapterSetException;
use \Core\ActiveRecordModelValidationException;
use \Core\ActiveRecordModelFinderException;
use \Core\Utility\Inflector;

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
     * @param bool $fetched
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
     * @return mixed|string
     */
    public function getName() {
        $class = get_class($this);
        $class = str_replace("Models\\", "", $class);
        $class = str_replace("\\", "", $class);
        return $class;
    }


    /**
     * Dynamic finder method.
     *
     * @return null
     * @throws \Core\ActiveRecordModelNoAdapterSetException
     */
    public function find() {
        $args = func_get_args();
        $numArgs = func_num_args();

        // at least one parameter should be passed
        if($numArgs == 0) {
            throw new ActiveRecordModelFinderException("Insufficient number of finder parameter.");
        }

        $find = $args[0]; // what to find?

        // Check whether options have been set.
        // Options are always in the second parameter and is an array.
        if($numArgs == 2 && is_array($args[1])) {
            $options = $args[1];
        }
        else {
            $options = array();
        }

        // Throw exception if adapter has not been set
        if(!static::$dbo) {
            throw new ActiveRecordModelNoAdapterSetException();
        }

        // Execute finder method
        if(is_int($find)) {
            return static::$dbo->findById($this->getName(), $find, $options);
        }
        elseif($find == "all") {
            return static::$dbo->findAll($this->getName(), $options);
        }
        elseif($find == "first") {
            return static::$dbo->findFirst($this->getName(), $options);
        }
        elseif($find == "last") {
            return static::$dbo->findLast($this->getName(), $options);
        }
        elseif($find == "one") {
            return static::$dbo->findOne($this->getName(), $options);
        }

        // method not found. throw exception.
        throw new ActiveRecordModelFinderException("Invalid finder: $find.");
    }


    /**
     * Returns an instance of this model.
     *
     * @static
     * @return mixed
     */
    protected static function newInstance() {
        $reflection = new \ReflectionClass(get_called_class());
        $model = $reflection->newInstance();
        return $model;
    }


    /**
     * Retrieves an object from model by primary key.
     *
     * @static
     * @param $pos
     * @param array $options
     * @return mixed
     */
    public static function findById($pos, $options = array()) {
        return call_user_func_array(
            array(static::newInstance(), "find"),
            array($pos, $options)
        );
    }


    /**
     * Retrieves all objects from model.
     *
     * @static
     * @param array $options
     * @return mixed
     */
    public static function findAll($options = array()) {
        return call_user_func_array(
            array(static::newInstance(), "find"),
            array("all", $options)
        );
    }


    /**
     * Retrieves first object from model.
     *
     * @static
     * @param array $options
     * @return mixed
     */
    public static function findFirst($options = array()) {
        return call_user_func_array(
            array(static::newInstance(), "find"),
            array("first", $options)
        );
    }


    /**
     * Retrieves last object from model.
     *
     * @static
     * @param array $options
     * @return mixed
     */
    public static function findLast($options = array()) {
        return call_user_func_array(
            array(static::newInstance(), "find"),
            array("last", $options)
        );
    }


    /**
     * Retrieves an object from model.
     *
     * @static
     * @param array $options
     * @return mixed
     */
    public static function findOne($options = array()) {
        return call_user_func_array(
            array(static::newInstance(), "find"),
            array("one", $options)
        );
    }


    /**
     * Saves objects.
     * @param array $options
     */
    public function save($options = array()) {
        if(static::$dbo) {
            // @todo: separate fields by models and their relationships
            // for now only for this model.
            $fields = array_keys((array) static::$fields); // fields of this model
            $data = array();
            foreach((array) $this->data as $key=>$value) {
                if(in_array($key, $fields)) {
                    $data[$key] = $value;
                }
            }

            // Create if hasn't fetched. Otherwise update.
            if($this->fetched == false) {
                $pkValue = static::$dbo->create($this->getName(), $data, $options);
                $this->__set(static::$primaryKey, $pkValue);
            }
            else {
                static::$dbo->update($this, $data, $options);
            }
        }
        else {
            throw new ActiveRecordModelNoAdapterSetException();
        }
    }


    /**
     * Deletes objects.
     * @param array $options
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
     * @return null
     */
    public function __get($key) {
        $key = Inflector::underscore($key);
        if(isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
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
     * @return array
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