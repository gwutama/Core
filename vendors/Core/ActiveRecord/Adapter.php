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
     *
     * @var string
     */
    protected static $dbh;

    /**
     * Model name
     *
     * @var string
     */
    protected $model;


    /**
     * Public constructor.
     */
    public function __construct() {
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
    abstract public function connect();


    /**
     * Closes database connection.
     *
     * @abstract
     */
    abstract public function disconnect();


    /**
     * Base for create operation.
     *
     * @abstract
     */
    abstract public function create($data, $options = array());


    /**
     * Base for read operation.
     *
     * @abstract
     */
    abstract public function read($options = array());


    /**
     * Base for update operation.
     *
     * @abstract
     */
    abstract public function update($data, $options = array());


    /**
     * Base for delete operation.
     *
     * @abstract
     */
    abstract public function delete();


    /**
     * Queries an adapter specific statement.
     *
     * @abstract
     * @param $statement
     */
    abstract public function query($statement);


    /**
     * Executes an adapter specific statement.
     *
     * @abstract
     * @param $statement
     */
    abstract public function execute($statement);


    /**
     * THe destructor disconnects the database handler.
     */
    public function __destruct() {
        $this->disconnect();
    }
}

?>

