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
     * @return array
     */
    public static function getHasOne($model) {
        $ref = new \ReflectionClass("\\Models\\".$model);
        $hasOne = $ref->getProperty("hasOne");
        if($hasOne == null) {
            return null;
        }
        return $hasOne->getValue();
    }


    /**
     * @return array
     */
    public static function getHasMany($model) {
        $ref = new \ReflectionClass("\\Models\\".$model);
        $hasMany = $ref->getProperty("hasMany");
        if($hasMany == null) {
            return null;
        }
        return $hasMany->getValue();
    }


    /**
     * @return array
     */
    public static function getBelongsTo($model) {
        $ref = new \ReflectionClass("\\Models\\".$model);
        $belongsTo = $ref->getProperty("belongsTo");
        if($belongsTo == null) {
            return null;
        }
        return $belongsTo->getValue();
    }


    /**
     * Returns the table name, which is the pluralized model name in lowercase.
     */
    public static function tableize($model) {
        // Get model name but remove the "Models\" namespace.
        $table = str_replace("Models\\", "", $model);
        $table = Inflector::tableize($table);
        return $table;
    }


    /**
     * Returns the primary key.
     *
     * @return mixed
     */
    public static function getPrimaryKey($model) {
        $ref = new \ReflectionClass("\\Models\\".$model);
        $primaryKey = $ref->getProperty("primaryKey");
        if($primaryKey == null) {
            return "id";
        }
        return $primaryKey->getValue();
    }


    /**
     * @param $primaryKey
     * @param $pos
     */
    abstract public function findById($modelName, $pos, $options = array());


    /**
     * @param $primaryKey
     */
    abstract public function findAll($modelName, $options = array());


    /**
     * @param $primaryKey
     * @param array $options
     */
    abstract public function findFirst($modelName, $options = array());


    /**
     * @param $primaryKey
     * @param array $options
     */
    abstract public function findLast($modelName, $options = array());


    /**
     * @param array $options
     */
    abstract public function findOne($modelName, $options = array());


    /**
     * Base for create operation.
     */
    abstract public function create($modelName, $data, $options = array());


    /**
     * Base for read operation.
     */
    abstract public function read($modelName, $options = array());


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

