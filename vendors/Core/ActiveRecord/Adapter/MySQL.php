<?php

namespace Core\ActiveRecord\Adapter;

use \Core\ActiveRecord\Adapter;
use \Core\ActiveRecord\Operator\MySQL as Op;
use \Core\ActiveRecord\QueryBuilder\MySQL as QueryBuilder;
use \Core\ActiveRecord\Model;
use \Core\ActiveRecord\ModelCollection;
use \Core\ActiveRecordAdapterConnectionException;
use \Core\ActiveRecordQueryException;
use \Core\Inflector;
use \PDO;
use \PDOException;


class MySQL extends Adapter {

    /**
     * Database object. PDO or PDO compliant.
     *
     * @var string
     */
    protected static $dbh;

    /**
     * DSN string for PDO connection.
     */
    protected $dsn;

    /**
     * MySQL username.
     */
    protected $username;

    /**
     * MySQL password.
     */
    protected $password;

    /**
     * Persistent connection. Boolean.
     */
    protected $persistent;


    /**
     * @param $dsn
     * @param $username
     * @param $password
     * @param $persistent
     */
    public function __construct($dsn, $username, $password, $persistent = false) {
        $this->dsn = $dsn;
        $this->username = $username;
        $this->password = $password;
        $this->persistent = $persistent;

        if(self::$dbh == null) {
            $this->connect();
        }
    }


    /**
     * The destructor disconnects the database handler.
     */
    public function __destruct() {
        if(static::$dbh) {
            $this->disconnect();
        }
    }


    /**
     * Connects to mysql server.
     */
    public function connect() {
        try {
            // Allow only one instance
            if(static::$dbh == null) {
                static::$dbh = new PDO($this->dsn, $this->username, $this->password,
                    array(PDO::ATTR_PERSISTENT => $this->persistent));
            }
        }
        catch(PDOException $e) {
            throw new ActiveRecordAdapterConnectionException("Cannot connect to MySQL server.");
        }
    }


    /**
     * Disconnects from mysql server.
     */
    public function disconnect() {
        self::$dbh = null;
    }


    /**
     * @param $statement
     * @return mixed
     */
    public function query($statement) {
        if(self::$dbh == null) {
            $this->connect();
        }
        return self::$dbh->query($statement);
    }


    /**
     * @param $statement
     */
    public function execute($statement) {
        if(self::$dbh == null) {
            $this->connect();
        }
        self::$dbh->exec($statement);
    }


    /**
     * Builds join conditions.
     *
     * @return string
     */
    private function buildJoinConditions($modelName, $models = array(), &$joinedTables = array(),
                                         &$selectedFields = array(), &$references = array()) {
        $tableName = static::tableize($modelName);

        foreach($models as $model) {
            if(is_array($model) == false) {
                $table = Inflector::tableize($model);
                $reference = sprintf("`%s`.`%s` = `%s`.`%s_id`", $tableName,
                    static::getPrimaryKey($modelName), $table, $tableName);
            }
            else {
                $table = Inflector::tableize($model["model"]);
                $reference = sprintf("`%s`.`%s` = `%s`.`%s`", $tableName,
                    static::getPrimaryKey($modelName), $table, $model["reference"]);
            }
            $references[] = $reference;
            $joinedTables[] = $table;

            // Append selected fields for this model
            foreach(static::getFields($model) as $field) {
                $selectedFields[] = sprintf("%s.%s AS %s_%s", $table, $field,
                                            Inflector::singularize($table), $field);
            }
        }

        $str = implode(" AND", $references);

        return $str;
    }


    /**
     * @param array $options
     */
    private function modifyJoinOptions($modelName, &$options = array()) {
        $selectedFields = array();

        // Try to determine which fields are selected
        $models = array();
        $table = static::tableize($modelName);

        if(isset($options["fields"]) && is_array($options["fields"]) && count($options["fields"]) > 0) {
            $fields = $options["fields"];
            foreach($fields as $field) {
                if(preg_match("/^[\w]+$/", $field)) {
                    $selectedFields[] = sprintf("%s.%s", $table, $field);
                }
                elseif(preg_match("/^([\w]+)\.[\w]+$/", $field, $matches)) {
                    $model = Inflector::classify($matches[1]);
                    if(!in_array($model, $models)) {
                        $models[] = $model;
                    }
                }
            }
        }
        else {
            // append fields of current table to the selected fields.
            foreach(static::getFields($modelName) as $field) {
                $selectedFields[] = sprintf("%s.%s", $table, $field);
            }

            $models = (array) static::getHasOne($modelName) + (array) static::getHasMany($modelName);
        }

        // Build join conditions
        $joinConditions = $this->buildJoinConditions($modelName, $models,
                                                     $joinedTables, $selectedFields);

        // modify options
        $options["fields"] = $selectedFields;
        $options["join"] = array(
            "tables" => $joinedTables,
            "conditions" => $joinConditions
        );
    }


    /**
     * @param $pos
     * @param $options array
     * @return mixed
     */
    public function findById($modelName, $pos, $options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($modelName, $options);

        // modify conditions
        $pkField = sprintf("%s.%s", static::tableize($modelName), static::getPrimaryKey($modelName));
        $options["conditions"] = Op::eq($pkField, $pos);

        // finally, get the object.
        $objects = $this->read($modelName, $options);
        if(count($objects)) {
            return $objects[0];
        }
        return null;
    }


    /**
     * @return mixed
     */
    public function findAll($modelName, $options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($modelName, $options);

        // finally, get the object
        $objects = new ModelCollection($modelName, $this, static::getPrimaryKey($modelName),
                                       $this->read($modelName, $options));
        return $objects;
    }


    /**
     * @param array $options
     * @return mixed
     */
    public function findFirst($modelName, $options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($modelName, $options);

        // Modify options
        $options["limit"] = 1;
        $options["order"] = sprintf("`%s` ASC", static::getPrimaryKey($modelName));

        // finally, get the object
        $objects = $this->read($modelName, $options);
        if(count($objects)) {
            return $objects[0];
        }
        return null;
    }


    /**
     * @param array $options
     * @return mixed
     */
    public function findLast($modelName, $options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($modelName, $options);

        // Modify options
        $options["limit"] = 1;
        $options["order"] = sprintf("`%s` DESC", static::getPrimaryKey($modelName));

        // finally, get the object
        $objects = $this->read($modelName, $options);
        if(count($objects)) {
            return $objects[0];
        }
        return null;
    }


    /**
     * @param array $options
     */
    public function findOne($modelName, $options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($modelName, $options);

        // Modify options
        $options["limit"] = 1;

        // finally, get the object
        $objects = $this->read($modelName, $options);
        if(count($objects)) {
            return $objects[0];
        }
        return null;
    }


    /**
     * Creates new records.
     *
     * @param $data
     * @param $options
     */
    public function create($modelName, $data, $options = array()) {
        if(self::$dbh == null) {
            $this->connect();
        }

        // Build query
        $table = static::tableize($modelName);
        $query = QueryBuilder::insert($table, $data, $options);

        // Set $data to prepared statement bind variables
        Op::setBinds($data);

        // Execute query with prepared statement
        try {
            // bind parameters
            $binds = Op::getBinds();
            Op::clearBinds();

            $stmt = self::$dbh->prepare($query);
            foreach($binds as $key=>&$value) {
                $stmt->bindParam($key, $value, $this->pdoType($value));
            }
            $stmt->execute();

            // return the id
            return self::$dbh->lastInsertId();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Selects records from database.
     *
     * @param $data
     * @param $options
     */
    public function read($modelName, $options = array()) {
        if(self::$dbh == null) {
            $this->connect();
        }

        // Build query
        $table = static::tableize($modelName);
        $query = QueryBuilder::select($table, $options);

        // Bind limit option if it is set
        if(isset($options["limit"])) {
            Op::setBind("core_query_limit", (int) $options["limit"]);

            // Bind offset option if it is set. Offset won't work without limit.
            if(isset($options["offset"])) {
                Op::setBind("core_query_offset", (int) $options["offset"]);
            }
        }

        // Execute query with prepared statement
        try {
            // bind parameters
            $binds = Op::getBinds();
            Op::clearBinds();

            $stmt = self::$dbh->prepare($query);
            foreach($binds as $key=>&$value) {
                $stmt->bindParam($key, $value, $this->pdoType($value));
            }
            $stmt->execute();

            // return as an object of the original model class
            return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
                "\\Models\\".$modelName, array(&$this, true)); // true: mark this object as fetched
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Updates records.
     *
     * @param $data
     * @param $options
     */
    public function update(Model $model, $data, $options = array()) {
        if(self::$dbh == null) {
            $this->connect();
        }

        // Build query
        $pk = $model->getPrimaryKey();
        $pkValue = $model->{$pk};
        $options["conditions"] = Op::eq($pk, $pkValue);

        $table = static::tableize($model->getName());
        $query = QueryBuilder::update($table, $data, $options);

        // Set $data to prepared statement bind variables
        Op::setBinds($data);

        // Bind limit option if it is set
        if(isset($options["limit"])) {
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }

        // Execute query with prepared statement
        try {
            // bind parameters
            $binds = Op::getBinds();
            Op::clearBinds();

            $stmt = self::$dbh->prepare($query);
            foreach($binds as $key=>&$value) {
                $stmt->bindParam($key, $value, $this->pdoType($value));
            }
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Updates multiple records.
     *
     * @param \Core\ActiveRecord\ModelCollection $models
     * @param array $options
     */
    public function updateAll(ModelCollection $models, $data, $options = array()) {
        if(self::$dbh == null) {
            $this->connect();
        }

        // Build query
        $pk = $models->getPrimaryKey();
        $pkValues = $models->getPrimaryKeyValues();
        $options["conditions"] = Op::in($pk, $pkValues);

        $table = static::tableize($models->getName());
        $query = QueryBuilder::update($table, $data, $options);

        // Set $data to prepared statement bind variables
        Op::setBinds($data);

        // Bind limit option if it is set
        if(isset($options["limit"])) {
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }

        // Execute query with prepared statement
        try {
            // bind parameters
            $binds = Op::getBinds();
            Op::clearBinds();

            $stmt = self::$dbh->prepare($query);
            foreach($binds as $key=>&$value) {
                $stmt->bindParam($key, $value, $this->pdoType($value));
            }
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Deletes records from database.
     *
     * @param $options
     */
    public function delete(Model $model, $options = array()) {
        if(self::$dbh == null) {
            $this->connect();
        }

        // Build query
        $pk = $model->getPrimaryKey();
        $pkValue = $model->{$pk};
        $options["conditions"] = Op::eq($pk, $pkValue);

        $table = static::tableize($model->getName());
        $query = QueryBuilder::delete($table, $options);

        // Bind limit option if it is set
        if(isset($first["limit"])) {
            Op::setBind("core_query_limit", (int) $first["limit"]);
        }

        // Execute query with prepared statement
        try {
            // bind parameters
            $binds = Op::getBinds();
            Op::clearBinds();

            $stmt = self::$dbh->prepare($query);
            foreach($binds as $key=>&$value) {
                $stmt->bindParam($key, $value, $this->pdoType($value));
            }
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Deletes multiple records.
     *
     * @param \Core\ActiveRecord\ModelCollection $models
     * @param array $options
     */
    public function deleteAll(ModelCollection $models, $options = array()) {
        if(self::$dbh == null) {
            $this->connect();
        }

        $pk = $models->getPrimaryKey();
        $pkValues = $models->getPrimaryKeyValues();
        $options["conditions"] = Op::in($pk, $pkValues);

        $table = static::tableize($models->getName());
        $query = QueryBuilder::delete($table, $options);

        // Bind limit option if it is set
        if(isset($options["limit"])) {
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }

        // Execute query with prepared statement
        try {
            // bind parameters
            $binds = Op::getBinds();
            Op::clearBinds();

            $stmt = self::$dbh->prepare($query);
            foreach($binds as $key=>&$value) {
                $stmt->bindParam($key, $value, $this->pdoType($value));
            }
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Returns the PDO type for specific values.
     *
     * @param $value
     */
    private function pdoType($value) {
        if(is_bool($value)) {
            return PDO::PARAM_BOOL;
        }
        if(is_null($value)) {
            return PDO::PARAM_NULL;
        }
        if(is_int($value)) {
            return PDO::PARAM_INT;
        }
        if(is_string($value)) {
            return PDO::PARAM_STR;
        }
    }

}

?>