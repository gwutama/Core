<?php

/**
 * <h1>Class Driver</h1>
 *
 * <p>
 * This class represents base for active record driver.
 * It implements the singleton pattern.
 * </p>
 */
abstract class ActiveRecord_Driver {

    /**
     * Database object. PDO or PDO compliant.
     */
    protected $dbo;


    /**
     * Protected constructor.
     */
    protected function __construct() {
    }


    /**
     * Returns an instance of database object.
     * Connects to database if database object is
     * still null.
     *
     * @return mixed
     */
    public function instance() {
        if($this->dbo == null) {
            $this->dbo = $this->connect();
        }
        return $this->dbo;
    }


    /**
     * Connects to database.
     *
     * @abstract
     */
    abstract function connect();


    /**
     * Closes database connection.
     *
     * @abstract
     */
    abstract function disconnect();


    /**
     * Base for create operation.
     *
     * @abstract
     */
    abstract public function create();


    /**
     * Base for read operation.
     *
     * @abstract
     */
    abstract public function read();


    /**
     * Base for update operation.
     *
     * @abstract
     */
    abstract public function update();


    /**
     * Base for delete operation.
     *
     * @abstract
     */
    abstract public function delete();

}

?>

