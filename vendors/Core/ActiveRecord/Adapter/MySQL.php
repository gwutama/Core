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
        $this->disconnect();
    }


    /**
     * Connects to mysql server.
     */
    public function connect() {
        try {
            // Allow only one instance
            if(self::$dbh == null) {
                self::$dbh = @new PDO($this->dsn, $this->username, $this->password,
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
    private function buildJoinConditions($models = array(), &$joinedTables = array(),
                                         &$references = array()) {
        if(count($models) == 0) {
            $models = (array)$this->hasOne + (array)$this->hasMany;
        }

        foreach($models as $model) {
            if(is_array($model) == false) {
                $table = Inflector::tableize($model);
                $reference = sprintf("`%s`.`%s` = `%s`.`%s_id`", $this->tableName,
                    $this->primaryKey, $table, $this->tableName);
            }
            else {
                $table = Inflector::tableize($model["model"]);
                $reference = sprintf("`%s`.`%s` = `%s`.`%s`", $this->tableName,
                    $this->primaryKey, $table, $model["reference"]);
            }
            $references[] = $reference;
            $joinedTables[] = $table;
        }

        $str = implode(" AND", $references);

        return $str;
    }


    /**
     * @param array $options
     */
    private function modifyJoinOptions(&$options = array()) {
        // Build join conditions
        $joinConditions = $this->buildJoinConditions(array(), $joinedTables);

        // modify options
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
    public function findById($pos, $options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($options);

        // modify conditions
        $pkField = $this->tableName.".".$this->primaryKey;
        $options["conditions"] = Op::eq($pkField, $pos);

        // finally, get the object.
        $objects = $this->read($options);
        if(count($objects)) {
            return $objects[0];
        }
        return null;
    }


    /**
     * @return mixed
     */
    public function findAll($options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($options);

        // finally, get the object
        $objects = new ModelCollection($this, $this->primaryKey, $this->read($options));
        return $objects;
    }


    /**
     * @param array $options
     * @return mixed
     */
    public function findFirst($options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($options);

        // Modify options
        $options["limit"] = 1;
        $options["order"] = sprintf("`%s` ASC", $this->primaryKey);

        // finally, get the object
        $objects = $this->read($options);
        if(count($objects)) {
            return $objects[0];
        }
        return null;
    }


    /**
     * @param array $options
     * @return mixed
     */
    public function findLast($options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($options);

        // Modify options
        $options["limit"] = 1;
        $options["order"] = sprintf("`%s` DESC", $this->primaryKey);

        // finally, get the object
        $objects = $this->read($options);
        if(count($objects)) {
            return $objects[0];
        }
        return null;
    }


    /**
     * @param array $options
     */
    public function findOne($options = array()) {
        // Build join conditions into $options
        $this->modifyJoinOptions($options);

        // Modify options
        $options["limit"] = 1;

        // finally, get the object
        $objects = $this->read($options);
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
    public function create($data, $options = array()) {
        if(self::$dbh == null) {
            $this->connect();
        }

        // Build query
        $query = $this->queryBuilder->insert($data, $options);

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
    public function read($options = array()) {
        if(self::$dbh == null) {
            $this->connect();
        }

        // Build query
        $query = $this->queryBuilder->select($options);

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
                $this->model, array(&$this, true)); // true: mark this object as fetched
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
        $query = $this->queryBuilder->update($data, $options);

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
        $query = $this->queryBuilder->update($data, $options);

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
        $query = $this->queryBuilder->delete($options);

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
        $query = $this->queryBuilder->delete($options);

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