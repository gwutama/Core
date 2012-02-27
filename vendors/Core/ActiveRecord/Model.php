<?php

namespace Core\ActiveRecord;

use \Core\ServiceContainer;
use \Core\Config;
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
     * Query values are saved in an array. Can be array of model objects
     * if findAll is called.
     *
     * @var string
     */
    protected $data;

    /**
     * Model profile. Defaults to "default".
     *
     * @var string
     */
    protected $databaseProfile = "default";

    /**
     * Whether data has been fetched or not.
     *
     * @var bool
     */
    protected $fetched = false;


    /**
     * Sets the driver DBO object.
     */
    public function __construct($fetched = false) {
        $this->fetched = $fetched;

        // Set adapter to dob
        $adapters = new AdapterServiceContainer();
        $this->dbo = $adapters->getService($this->databaseProfile);

        $this->dbo->setModel(get_class($this));
        $this->dbo->setPrimaryKey($this->primaryKey);
        $this->dbo->setHasOne($this->hasOne);
        $this->dbo->setHasMany($this->hasMany);
        $this->dbo->setBelongsTo($this->belongsTo);
    }


    /**
     * Returns current model's primary key.
     *
     * @return string
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
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
     * Retrieves an object from model by primary key.
     *
     * @param $pos
     */
    public function findById($pos, $options = array()) {
        if($this->dbo) {
            return $this->dbo->findById($pos, $options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Retrieves all objects from model.
     *
     * @param array $options
     */
    public function findAll($options = array()) {
        if($this->dbo) {
            return $this->dbo->findAll($options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Retrieves first object from model.
     *
     * @param array $options
     */
    public function findFirst($options = array()) {
        if($this->dbo) {
            return $this->dbo->findFirst($options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Retrieves last object from model.
     *
     * @param array $options
     */
    public function findLast($options = array()) {
        if($this->dbo) {
            return $this->dbo->findLast($options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Retrieves an object from model.
     *
     * @param array $options
     */
    public function findOne($options = array()) {
        if($this->dbo) {
            return $this->dbo->findOne($options);
        }
        throw new ActiveRecordModelNoAdapterSetException();
    }


    /**
     * Saves objects.
     */
    public function save($options = array()) {
        if($this->dbo) {
            if($this->fetched == false) {
                $pkValue = $this->dbo->create($this->data, $options);
                $this->__set($this->primaryKey, $pkValue);
            }
            else {
                $this->dbo->update($this, $this->data, $options);
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
        if($this->dbo) {
            $this->dbo->delete($this, $options);
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
        if($this->dbo) {
            $this->dbo->execute($statement);
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
        if($this->dbo) {
            return $this->dbo->query($statement);
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
