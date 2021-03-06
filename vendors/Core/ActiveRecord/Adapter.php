<?php

namespace Core\ActiveRecord;
use \Core\Utility\Inflector;

/**
 * <h1>Interface Adapter</h1>
 *
 * <p>
 * This interface represents base for active record driver.
 * </p>
 */
abstract class Adapter {

    /**
     * @param $model
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
     * @param $model
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
     * @param $model
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
     * @param $model
     * @return mixed
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
     * @param $model
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
     * Returns field names of a model.
     *
     * @static
     * @param $model
     * @return array
     */
    public static function getFields($model) {
        // We don't need the type and whether nullable or not.
        // We only need the field name.
        $ref = new \ReflectionClass("\\Models\\".$model);
        $fields = $ref->getProperty("fields");

        if($fields == null) {
            return array();
        }

        $val = $fields->getValue();

        $allFields = array_keys($val);
        return $allFields;
    }


    /**
     * @param $modelName
     * @param $pos
     * @param array $options
     * @internal param $primaryKey
     */
    abstract public function findById($modelName, $pos, $options = array());


    /**
     * @param $modelName
     * @param array $options
     * @internal param $primaryKey
     */
    abstract public function findAll($modelName, $options = array());


    /**
     * @param $modelName
     * @param array $options
     * @internal param $primaryKey
     */
    abstract public function findFirst($modelName, $options = array());


    /**
     * @param $modelName
     * @param array $options
     * @internal param $primaryKey
     */
    abstract public function findLast($modelName, $options = array());


    /**
     * @param $modelName
     * @param array $options
     */
    abstract public function findOne($modelName, $options = array());


    /**
     * Base for create operation.
     * @param $modelName
     * @param $data
     * @param array $options
     */
    abstract public function create($modelName, $data, $options = array());


    /**
     * Base for read operation.
     * @param $modelName
     * @param array $options
     */
    abstract public function read($modelName, $options = array());


    /**
     * Base for update operation.
     * @param \Core\ActiveRecord\Model $model
     * @param $data
     * @param array $options
     */
    abstract public function update(Model $model, $data, $options = array());


    /**
     * Base for multiple update operation.
     * @param \Core\ActiveRecord\ModelCollection $model
     * @param $data
     * @param array $options
     */
    abstract public function updateAll(ModelCollection $model, $data, $options = array());


    /**
     * Base for delete operation.
     * @param \Core\ActiveRecord\Model $model
     * @param array $options
     */
    abstract public function delete(Model $model, $options = array());


    /**
     * Base for multiple delete operation.
     * @param \Core\ActiveRecord\ModelCollection $models
     * @param array $options
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

