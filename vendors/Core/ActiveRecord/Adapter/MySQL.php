<?php

namespace Core\ActiveRecord\Adapter;

use \Core\ActiveRecord\Adapter;
use \Core\ActiveRecord\Operator\MySQL as Op;
use \Core\ActiveRecord\QueryBuilder\MySQL as QueryBuilder;
use \Core\ActiveRecord\ModelCollection;
use \Core\ActiveRecordAdapterConnectionException;
use \Core\ActiveRecordQueryException;
use \Core\Inflector;
use \PDO;
use \PDOException;


class MySQL implements Adapter {

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
     * Model name
     *
     * @var string
     */
    protected $model;


    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;


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
     * Sets the model name.
     *
     * @param $model    Model name.
     */
    public function setModel($model) {
        $this->model = $model;
        $this->queryBuilder = new QueryBuilder($model);
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
        return self::$dbh->query($statement);
    }


    /**
     * @param $statement
     */
    public function execute($statement) {
        self::$dbh->exec($statement);
    }


    /**
     * @param $primaryKey
     * @param $pos
     * @return mixed
     */
    public function findById($primaryKey, $pos) {
        $objects = $this->read(array(
            "conditions" => Op::eq($primaryKey, $pos)
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
        // Build query
        $query = $this->queryBuilder->select($options);

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

            // return as an object of the original model class
            return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE,
                $this->model, array(&$this));
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
    public function update($data, $options = array()) {
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
    public function delete() {
        // Build query based on passed parameters.
        $args = func_get_args();
        $numargs = func_num_args();

        $first = @$args[0];
        $second = @$args[1];

        if($numargs == 1) {
            // $first is the options
            $query = $this->queryBuilder->delete($first);

            // Bind limit option if it is set
            if(isset($first["limit"])) {
                Op::setBind("core_query_limit", (int) $first["limit"]);
            }
        }
        elseif($numargs == 2) {
            if(is_array($second)) {
                // $first is the primary key and $second can be an array of options or an array of Model objects.
                // To check whether it is an array of Model objects, we only check the first array value
                // whether it is an instance of Model.
                if($second[0] instanceof \Core\ActiveRecord\Model) {
                    // Delete multiple records. Build IN() statement.
                    $in = array();
                    foreach($second as $obj) {
                        $in[] = $obj->{$first}; // first is the primary key
                    }

                    $query = $this->queryBuilder->delete(array(
                        "conditions" => Op::in($first, $in)  // first is the primary key
                    ));
                }
                else {
                    return; // todo: throw an exception instead?
                }
            }
            elseif($second instanceof \Core\ActiveRecord\Model) {
                // $first is the primary key and $second is a single model to be deleted.
                $val = $second->{$first}; // value of the primary key field
                $query = $this->queryBuilder->delete(array(
                    "conditions" => Op::eq($first, $val)
                ));
            }
            else {
                return; // todo: throw an exception instead?
            }
        }
        else {
            return; // todo: throw an exception instead?
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