<?php

/**
 * <h1>Class Core_ActiveRecord_Adapter</h1>
 *
 * <p>
 * This class represents base for active record driver.
 * It implements the singleton pattern.
 * </p>
 */
abstract class Core_ActiveRecord_Adapter {

    /**
     * Database object. PDO or PDO compliant.
     */
    protected $dbh;


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
        if($this->dbh == null) {
            $this->dbh = $this->connect();
        }
        return $this->dbh;
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
     * Base for create operation.
     *
     * @abstract
     */
    abstract public function create($data, $options);


    /**
     * Base for read operation.
     *
     * @abstract
     */
    abstract public function read($data, $options);


    /**
     * Base for update operation.
     *
     * @abstract
     */
    abstract public function update($data, $options);


    /**
     * Base for delete operation.
     *
     * @abstract
     */
    abstract public function delete($data, $options);


    /**
     *
     */
    protected function getSingular() {

    }
}

?>

