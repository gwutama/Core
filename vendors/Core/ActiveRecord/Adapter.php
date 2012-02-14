<?php

namespace Core\ActiveRecord;

/**
 * <h1>Class Adapter</h1>
 *
 * <p>
 * This class represents base for active record driver.
 * It implements the singleton pattern.
 * </p>
 */
abstract class Adapter {

    /**
     * Database object. PDO or PDO compliant.
     */
    protected static $dbh;
    protected $model;


    /**
     * Public constructor.
     */
    public function __construct() {
        $this->model = get_class($this);
        if(self::$dbh == null) {
            $this->connect();
        }
    }


    /**
     * Sets the model name.
     *
     * @param $model    Model name.
     */
    public function setModel($model) {
        $this->model = $model;
    }


    /**
     * Returns the model name.
     *
     * @return Model name.
     */
    public function getModel() {
        return $this->model;
    }


    /**
     * Connects to database.
     *
     * @abstract
     */
    abstract protected function connect();


    /**
     * Closes database connection.
     *
     * @abstract
     */
    abstract protected function disconnect();


    /**
     * Gets executed before inserting new records.
     *
     * @abstract
     */
    abstract public function beforeCreate();


    /**
     * Base for create operation.
     *
     * @abstract
     */
    abstract public function create($data, $options = array());


    /**
     * Gets executed before selecting records.
     *
     * @abstract
     */
    abstract public function beforeRead();


    /**
     * Base for read operation.
     *
     * @abstract
     */
    abstract public function read($data, $options = array());


    /**
     * Gets executed before updating records.
     *
     * @abstract
     */
    abstract public function beforeUpdate();


    /**
     * Base for update operation.
     *
     * @abstract
     */
    abstract public function update($data, $options = array());


    /**
     * Gets executed before deleting records.
     *
     * @abstract
     */
    abstract public function beforeDelete();


    /**
     * Base for delete operation.
     *
     * @abstract
     */
    abstract public function delete($options = array());

}

?>

