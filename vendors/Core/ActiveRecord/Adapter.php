<?php

namespace Core\ActiveRecord;
use \Core\Inflector;

/**
 * <h1>Interface Adapter</h1>
 *
 * <p>
 * This interface represents base for active record driver.
 * </p>
 */
abstract class Adapter {


    /**
     * Model name
     *
     * @var string
     */
    protected $model;


    /**
     * @var
     */
    protected $hasOne = array();


    /**
     * @var
     */
    protected $hasMany = array();


    /**
     * @var
     */
    protected $belongsTo = array();


    /**
     * @var
     */
    protected $primaryKey;


    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;


    /**
     * Sets the model name.
     *
     * @param $model    Model name.
     */
    public function setModel($model) {
        $this->model = $model;

        // Sets the query builder. For this we only need the adapter class name
        $class = get_class($this);
        $adapter = str_replace("Core\\ActiveRecord\\Adapter\\", "", $class);

        // Then create an instance of Core\ActiveRecord\QueryBuilder\Adaptername
        $builder = "Core\\ActiveRecord\\QueryBuilder\\".$adapter;
        $this->queryBuilder = new $builder($model);
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
     * @param array $models
     */
    public function setHasOne($models = array()) {
        $this->hasOne = $models;
    }


    /**
     * @return array
     */
    public function getHasOne() {
        return $this->hasOne;
    }


    /**
     * @param array $models
     */
    public function setHasMany($models = array()) {
        $this->hasMany = $models;
    }


    /**
     * @return array
     */
    public function getHasMany() {
        return $this->hasMany;
    }


    /**
     * @param array $models
     */
    public function setBelongsTo($models = array()) {
        $this->belongsTo = $models;
    }


    /**
     * @return array
     */
    public function getBelongsTo() {
        return $this->belongsTo;
    }


    /**
     * Returns the table name, which is the pluralized model name in lowercase.
     */
    public function getTableName() {
        // Get model name but remove the "Models\" namspace.
        $table = str_replace("Models\\", "", $this->model);
        $table = Inflector::tableize($table);
        return $table;
    }


    /**
     * Sets the primar key.
     *
     * @param $key
     */
    public function setPrimaryKey($key) {
        $this->primaryKey = $key;
    }


    /**
     * Returns the primary key.
     *
     * @return mixed
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }


    /**
     * @param $primaryKey
     * @param $pos
     */
    abstract public function findById($pos);


    /**
     * @param $primaryKey
     */
    abstract public function findAll();


    /**
     * @param $primaryKey
     * @param array $options
     */
    abstract public function findFirst($options = array());


    /**
     * @param $primaryKey
     * @param array $options
     */
    abstract public function findLast($options = array());


    /**
     * @param array $options
     */
    abstract public function findOne($options = array());


    /**
     * Base for create operation.
     */
    abstract public function create($data, $options = array());


    /**
     * Base for read operation.
     */
    abstract public function read($options = array());


    /**
     * Base for update operation.
     */
    abstract public function update(Model $model, $data, $options = array());


    /**
     * Base for multiple update operation.
     */
    abstract public function updateAll(ModelCollection $model, $data, $options = array());


    /**
     * Base for delete operation.
     */
    abstract public function delete(Model $model, $options = array());


    /**
     * Base for multiple delete operation.
     */
    abstract public function deleteAll(ModelCollection $models, $options = array());


    /**
     * Queries an adapter specific statement.
     *
     * @param $statement
     */
    abstract public function query($statement);


    /**
     * Executes an adapter specific statement.
     *
     * @param $statement
     */
    abstract public function execute($statement);

}

?>

