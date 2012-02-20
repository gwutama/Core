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
    public function __construct(Adapter $dbo, $fetched = false) {
        $this->dbo = $dbo;
        $model = get_class($this);
        $this->dbo->setModel($model);
        $this->fetched = $fetched;
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
    public function findById($pos) {
        return $this->dbo->findById($this->primaryKey, $pos);
    }


    /**
     * Retrieves all objects from model.
     *
     * @param array $options
     */
    public function findAll($options = array()) {
        return $this->dbo->findAll($this->primaryKey, $options); // returns ModelCollection
    }


    /**
     * Retrieves first object from model.
     *
     * @param array $options
     */
    public function findFirst($options = array()) {
        return $this->dbo->findFirst($this->primaryKey, $options);
    }


    /**
     * Retrieves last object from model.
     *
     * @param array $options
     */
    public function findLast($options = array()) {
        return $this->dbo->findLast($this->primaryKey, $options);
    }


    /**
     * Retrieves an object from model.
     *
     * @param array $options
     */
    public function findOne($options = array()) {
        return $this->dbo->findOne($options);
    }


    /**
     * Saves objects.
     */
    public function save($options = array()) {
        if($this->fetched == false) {
            $this->dbo->create($this->data, $options);
        }
        else {
            $this->dbo->update($this, $this->data, $options);
        }
    }

    /**
     * Deletes objects.
     */
    public function delete($options = array()) {
        $this->dbo->delete($this, $options);
    }


    /**
     * Executes an adapter specific statement.
     *
     * @param $statement
     */
    public function execute($statement) {
        $this->dbo->execute($statement);
    }


    /**
     * Queries an adapter specific statement.
     *
     * @param $statement
     * @return mixed
     */
    public function query($statement) {
        return $this->dbo->query($statement);
    }


    /**
     * Magic method to retrieve query result by key.
     *
     * @param $key      Object key.
     */
    public function __get($key) {
        return @$this->data[$key];
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
