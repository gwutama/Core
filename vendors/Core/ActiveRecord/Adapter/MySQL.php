<?php

namespace Core\ActiveRecord\Adapter;

use \Core\ActiveRecord\Adapter;
use \Core\ActiveRecord\Operator\MySQL as Op;
use \Core\Config;
use \Core\ActiveRecordAdapterConnectionException;
use \Core\ActiveRecordQueryException;
use \Core\Inflector;
use \PDO;
use \PDOException;

class MySQL extends Adapter {

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
     * Connects to mysql server.
     */
    protected function connect() {
        $this->beforeConnect();
        try {
            $this->dbh = new PDO($this->dsn, $this->username, $this->password,
                array(PDO::ATTR_PERSISTENT => $this->persistent));
        }
        catch(PDOException $e) {
            throw new ActiveRecordAdapterConnectionException("Cannot connect to MySQL server.");
        }
    }


    /**
     * Gets executed before connect. In this case, set dsn, username and password
     * default to standard configuration in database.php.
     */
    protected function beforeConnect() {
        $config = Config::get("database");

        if(Config::get("debug") == true) {
            $this->dsn = $config["debug"]["dsn"];
            $this->username = $config["debug"]["username"];
            $this->password = $config["debug"]["password"];
            $this->persistent = $config["debug"]["persistent"];
        }
        else {
            $this->dsn = $config["production"]["dsn"];
            $this->username = $config["production"]["username"];
            $this->password = $config["production"]["password"];
            $this->persistent = $config["production"]["persistent"];
        }
    }


    /**
     * Disconnects from mysql server.
     */
    protected function disconnect() {
        $this->dbh = null;
    }


    /**
     * <p>
     * Builds insert query (prepared statement).
     * http://dev.mysql.com/doc/refman/5.0/en/insert.html.
     * </p>
     *
     * <p>
     * The most complex query for $options could be somewhat like this:
     * </p>
     * <code>
     * $options = array(
     *      "select" => MySQL::selectQuery("Model", array(
     *          "fields" => array("Model.foo", "Model.bar", "Model.hello"),
     *          "conditions" => array(
     *              Operator::boolOr(
     *                  Operator::boolAnd(
     *                      Operator::equals("foo", "bar"),
     *                      Operator::notEquals("baz", "blah"),
     *                      Operator::notEquals("hello", "world"),
     *                  ),
     *                  Operator::equals("ThisModel.field", "1")
     *              )
     *          )
     *      )),
     *      "on duplicate key update" => array("foo='bar'", "baz='blah'"),
     * );
     * </code>
     *
     * @param $data     Data to insert. Array of key => value pairs.
     * @param $options  Options. See example above.
     * @return string   SQL Query (prepared statement).
     */
    public static function insertQuery($model, $data, $options = array()) {
        // Set $data to prepared statement bind variables
        Op::setBinds($data);

        // Determine query format.
        // Lowercase all $options keys then find for "select" option.
        $insertWithSelect = in_array(strtolower("select"), array_map("strtolower", array_keys($options)));
        if($insertWithSelect == false) {
            // First case: Standard insert query
            // 1st %s => table name
            // 2nd %s => fields
            // 3rd %s => Binding parameters (series of question marks "?")
            // 4th &s => INSERT ... ON DUPLICATE KEY UPDATE syntax. See link to manual above.
            $query = "INSERT INTO %s(%s) VALUES(%s) %s";
        }
        else {
            // Second case: Insert query with select.
            // If "INSERT ... SELECT" is meant to be executed, then query is as following:
            // http://dev.mysql.com/doc/refman/5.5/en/insert-select.html
            // 1st %s => table name
            // 2nd %s => fields
            // 3rd %s => SELECT statement
            $query = "INSERT INTO %s(%s) %s";
        }

        // Pluralize model name
        $tableName = Inflector::tableize($model);

        // Build field names based on $data keys
        $keys = array_keys($data);
        $fields = implode(", ", $keys);
        $fields = strtolower($fields);

        // Statements are bound with bind variables
        $binds = implode(", ", $keys);
        $binds = preg_replace("/([\w0-9_]+)/", ":$1", $binds);

        // Default query parts
        $onDuplicateKeyUpdate = "";
        $select = "";

        // Work on the passed options
        foreach($options as $key=>$value) {
            $key = strtoupper($key);

            // Check for "INSERT ... SELECT"
            if($key = "SELECT") {
                $select = $value;
            }
            else if($key == "ON DUPLICATE KEY UPDATE") {
                // Check for "INSERT ... ON DUPLICATE KEY UPDATE"
                // If value is an array, then build: col_name=expr, col_name2=expr2, ...
                // Otherwise just append the value.
                $onDuplicateKeyUpdate = "ON DUPLICATE KEY UPDATE ";
                if( is_array($value) ) {
                    $onDuplicateKeyUpdate .= implode(", ", $value);
                }
                else {
                    $onDuplicateKeyUpdate .= $value;
                }
            }
        }

        // Refer to first and second cases above.
        if($insertWithSelect == false) {
            return trim(sprintf($query, $tableName, $fields, $binds, $onDuplicateKeyUpdate));
        }
        else {
            return trim(sprintf($query, $tableName, $select));
        }
    }


    /**
     * Gets executed before inserting new records.
     *
     * @param $data
     * @param $options
     */
    public function beforeCreate() {
    }


    /**
     * Creates new records.
     *
     * @param $data
     * @param $options
     */
    public function create($data, $options = array()) {
        Op::clearBinds();
        $this->beforeCreate();

        // Build query
        $query = self::insertQuery($this->model, $data, $options);

        // Execute query with prepared statement
        try {
            $stmt = $this->dbh->prepare($query);
            // bind parameters
            foreach(Op::getBinds() as $key=>$value) {
                $stmt->bindParam($key, $value);
            }
            $stmt->execute();
            Op::clearBinds();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Builds select query.
     *
     * see http://dev.mysql.com/doc/refman/5.0/en/select.html
     *
     * SELECT
     * [ALL | DISTINCT | DISTINCTROW ]
     * select_expr [, select_expr ...]
     * [FROM table_references
     * [WHERE where_condition]
     * [GROUP BY {col_name | expr | position}
     * [ASC | DESC], ... [WITH ROLLUP]]
     * [HAVING where_condition]
     * [ORDER BY {col_name | expr | position}
     * [ASC | DESC], ...]
     * [LIMIT {[offset,] row_count | row_count OFFSET offset}]
     *
     * @param $data
     * @param $options
     * @return string
     */
    public static function selectQuery($model, $options = array()) {
        /*
        if(isset($options["join"])) {
            $query = "SELECT %s FROM %s%s%s%s%s%s";
        }
        else {

        }
        */

        // Build query
        // 1. Build (field1, field2, ..) and (?, ?, ..)
        if( isset($options["fields"]) ) {
            $fields = implode(",", $options["fields"]);
        }
        else {
            $fields = "*";
        }

        // Build condition

        // Build order
        if( isset($options["order"]) ) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        if( isset($options["limit"]) ) {
            $limit = "LIMIT :core_query_limit";
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }
        else {
            $limit = "";
        }

        return "";
    }


    /**
     * Gets executed before selecting records.
     */
    public function beforeRead() {
    }


    /**
     * Selects records from database.
     *
     * @param $data
     * @param $options
     */
    public function read($data, $options = array()) {
        Op::clearBinds();
        $this->beforeRead();

        // Build query
        $query = self::selectQuery($this->model, $data, $options);

        // Execute query with prepared statement
        try {
            $stmt = $this->dbh->prepare($query);
            // bind parameters
            foreach(Op::getBinds() as $key=>$value) {
                $stmt->bindParam($key, $value);
            }
            $stmt->execute();
            Op::clearBinds();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Builds update query. Only supports single table updates.
     *
     * Single-table syntax:
     * UPDATE [LOW_PRIORITY] [IGNORE] table_reference
     * SET col_name1={expr1|DEFAULT} [, col_name2={expr2|DEFAULT}] ...
     * [WHERE where_condition]
     * [ORDER BY ...]
     * [LIMIT row_count]
     *
     * see http://dev.mysql.com/doc/refman/5.0/en/update.html
     *
     * @param $data
     * @param $options
     * @return string
     */
    public static function updateQuery($model, $data, $options = array()) {
        // Set $data to prepared statement bind variables
        Op::setBinds($data);

        // 1st %s : table name
        // 2nd %s : key-value pairs
        // 3rd %s : where conditions
        // 4th %s : order conditions
        // 5th %s : limit
        $query = "UPDATE %s SET %s%s%s%s";

        // Pluralize model name
        $tableName = Inflector::tableize($model);

        // Build key-value pairs
        $sets = "";
        $count = count($data);
        $i = 0;
        foreach((array) $data as $key=>$value) {
            if($i < $count-1) {
                $sets .= "$key = :$key, ";
            }
            else {
                $sets .= "$key = :$key ";
            }
            ++$i;
        }

        // Build condition
        if( isset($options["conditions"]) ) {
            $conditions = "WHERE ".$options["conditions"]." ";
        }
        else {
            $conditions = "";
        }

        // Build order
        if( isset($options["order"]) ) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        if( isset($options["limit"]) ) {
            $limit = "LIMIT :core_query_limit";
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }
        else {
            $limit = "";
        }

        return trim(sprintf($query, $tableName, $sets, $conditions, $order, $limit));
    }


    /**
     * Gets executed before updating records.
     */
    public function beforeUpdate() {
    }


    /**
     * Updates records.
     *
     * @param $data
     * @param $options
     */
    public function update($data, $options = array()) {
        Op::clearBinds();
        $this->beforeUpdate();

        // Build query
        $query = self::updateQuery($this->model, $data, $options);

        // Execute query with prepared statement
        try {
            $stmt = $this->dbh->prepare($query);
            // bind parameters
            foreach(Op::getBinds() as $key=>$value) {
                $stmt->bindParam($key, $value);
            }
            $stmt->execute();
            Op::clearBinds();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Builds delete query.
     *
     * Single-table syntax:
     * DELETE [LOW_PRIORITY] [QUICK] [IGNORE] FROM tbl_name
     * [WHERE where_condition]
     * [ORDER BY ...]
     * [LIMIT row_count]
     *
     * see http://dev.mysql.com/doc/refman/5.0/en/delete.html
     *
     * @param $data
     * @param $options
     * @return string
     */
    public static function deleteQuery($model, $options = array()) {
        // [WHERE where_condition]
        // [ORDER BY ...]
        // [LIMIT row_count]
        //
        // 1st %s : Table name
        // 2nd %s : WHERE condition
        // 3rd %s : ORDER condition
        // 4st %s : LIMIT
        $query = "DELETE FROM %s %s%s%s";

        // Pluralize model name
        $tableName = Inflector::tableize($model);

        // Build condition
        if( isset($options["conditions"]) ) {
            $conditions = "WHERE ".$options["conditions"]." ";
        }
        else {
            $conditions = "";
        }

        // Build order
        if( isset($options["order"]) ) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        if( isset($options["limit"]) ) {
            $limit = "LIMIT :core_query_limit";
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }
        else {
            $limit = "";
        }

        return trim(sprintf($query, $tableName, $conditions, $order, $limit));
    }


    /**
     * Gets executed before deleting records.
     */
    public function beforeDelete() {
    }


    /**
     * Deletes records from database.
     *
     * @param $options
     */
    public function delete($options = array()) {
        Op::clearBinds();
        $this->beforeDelete();

        // Build query
        $query = self::deleteQuery($this->model, $options);

        // Execute query with prepared statement
        try {
            $stmt = $this->dbh->prepare($query);
            // bind parameters
            foreach(Op::getBinds() as $key=>$value) {
                $stmt->bindParam($key, $value);
            }
            $stmt->execute();
            Op::clearBinds();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }

}

?>
