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
     * @return array
     */
    private function buildJoinConditions() {
        $models = (array)$this->hasOne + (array)$this->hasMany;
        $references = array();

        $currTable = $this->getTableName();

        foreach($models as $model) {
            if(is_array($model) == false) {
                $table = Inflector::tableize($model);
                $references[] = "$currTable.id = ".$table.".".$currTable."_id";
            }
            else {
                $table = Inflector::tableize($model["model"]);
                $references[] = "$currTable.id = ".$table.".".$model["reference"];
            }
        }

        $str = implode(" AND", $references);

        return $str;
    }


    /**
     * @param $primaryKey
     * @param $pos
     * @return mixed
     */
    public function findById($primaryKey, $pos) {
        // tableize model names
        $tables = (array) $this->hasOne + (array) $this->hasMany;
        $joinTables = array();
        foreach($tables as $table) {
            $joinTables = Inflector::tableize($table);
        }

        // Build join conditions
        $joinConditions = $this->buildJoinConditions();

        // Get current table name
        $currTable = $this->getTableName();

        // finaly get the object.
        $objects = $this->read(array(
            "conditions" => Op::eq($currTable.".".$primaryKey, $pos),
            "join" => array(
                "tables" => $joinTables,
                "conditions" => $joinConditions
            )
        ));
        return $objects[0];
    }


    /**
     * @return mixed
     */
    public function findAll($primaryKey) {
        return new ModelCollection($this, $primaryKey, $this->read());
    }


    /**
     * @param $primaryKey
     * @param array $options
     * @return mixed
     */
    public function findFirst($primaryKey, $options = array()) {
        $objects = $this->read(array(
            "conditions" => @$options["conditions"],
            "limit" => 1,
            "order" => "`$primaryKey` ASC"
        ));
        return $objects[0];
    }


    /**
     * @param $primaryKey
     * @param array $options
     * @return mixed
     */
    public function findLast($primaryKey, $options = array()) {
        $objects = $this->read(array(
            "conditions" => @$options["conditions"],
            "limit" => 1,
            "order" => "`$primaryKey` DESC"
        ));
        return $objects[0];
    }


    /**
     * @param array $options
     */
    public function findOne($options = array()) {
        $objects = $this->read(array(
            "conditions" => @$options["conditions"],
            "limit" => 1
        ));
        return $objects[0];
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