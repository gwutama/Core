<?php

namespace Core\ActiveRecord;

/**
 * <h1>Interface Adapter</h1>
 *
 * <p>
 * This interface represents base for active record driver.
 * </p>
 */
interface Adapter {

    /**
     * Sets the model name.
     *
     * @param $model    Model name.
     */
    public function setModel($model);


    /**
     * Returns the model name.
     *
     * @return Model name.
     */
    public function getModel();


    /**
     * @param $primaryKey
     * @param $pos
     */
    public function findById($primaryKey, $pos);


    /**
     * @param $primaryKey
     */
    public function findAll($primaryKey);


    /**
     * @param $primaryKey
     * @param array $options
     */
    public function findFirst($primaryKey, $options = array());


    /**
     * @param $primaryKey
     * @param array $options
     */
    public function findLast($primaryKey, $options = array());


    /**
     * @param array $options
     */
    public function findOne($options = array());


    /**
     * Base for create operation.
     */
    public function create($data, $options = array());


    /**
     * Base for read operation.
     */
    public function read($options = array());


    /**
     * Base for update operation.
     */
    public function update($data, $options = array());


    /**
     * Base for delete operation.
     */
    public function delete();


    /**
     * Queries an adapter specific statement.
     *
     * @param $statement
     */
    public function query($statement);


    /**
     * Executes an adapter specific statement.
     *
     * @param $statement
     */
    public function execute($statement);

}

?>

