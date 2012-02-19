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
     * Model profile. Defaults to "default".
     *
     * @var string
     */
    protected $databaseProfile = "default";


    /**
     * Whether $data has been fetched.
     *
     * @var bool
     */
    protected $fetched = false;


    /**
     * Sets the driver DBO object.
     */
    public function __construct(Adapter $dbo) {
        $this->dbo = $dbo;
        $model = get_class($this);
        $this->dbo->setModel($model);
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
     * Saves an object.
     */
    public function save() {
        if($this->fetched == false) {
            $this->dbo->create($this->data);
        }
        else {
            $this->dbo->update($this->data);
        }
    }

    /**
     * Deletes objects. With $options : Deletes with query
     * Without $options : Delete current object(s).
     */
    public function delete($options = array()) {
        if(count($options)) {
            $this->dbo->delete($options);
        }
        else {
            $this->dbo->delete($this->primaryKey, $this);
        }
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
