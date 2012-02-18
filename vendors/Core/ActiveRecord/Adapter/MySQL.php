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
        parent::__construct();
    }


    /**
     * Connects to mysql server.
     */
    protected function connect() {
        try {
            self::$dbh = @new PDO($this->dsn, $this->username, $this->password,
                array(PDO::ATTR_PERSISTENT => $this->persistent));
        }
        catch(PDOException $e) {
            throw new ActiveRecordAdapterConnectionException("Cannot connect to MySQL server.");
        }
    }


    /**
     * Disconnects from mysql server.
     */
    protected function disconnect() {
        self::$dbh = null;
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
        if(isset($options["select"]) == false) {
            // First case: Standard insert query
            // 1st %s => table name
            // 2nd %s => fields
            // 3rd %s => Binding parameters (series of question marks "?")
            // 4th %s => INSERT ... ON DUPLICATE KEY UPDATE syntax. See link to manual above.
            $query = "INSERT INTO `%s`(%s) VALUES(%s) %s";
        }
        else {
            // Second case: Insert query with select.
            // If "INSERT ... SELECT" is meant to be executed, then query is as following:
            // http://dev.mysql.com/doc/refman/5.5/en/insert-select.html
            // 1st %s => table name
            // 2nd %s => fields
            // 3rd %s => SELECT statement
            $query = "INSERT INTO `%s`(%s) %s";
        }

        // Pluralize model name
        $tableName = Inflector::tableize($model);

        // Build field names based on $data keys
        $keys = array_keys($data);
        $fields = implode(", ", $keys);
        $fields = strtolower($fields);
        $fields = preg_replace("/([\w0-9_]+)/", "`$1`", $fields);

        // Statements are bound with bind variables
        $binds = implode(", ", $keys);
        $binds = preg_replace("/([\w0-9_]+)/", ":$1", $binds);

        // Default query parts
        $onDuplicateKeyUpdate = "";
        $select = "";

        // Check for "INSERT ... SELECT"
        if(isset($options["select"])) {
            $select = $options["select"];
        }

        if(isset($options["on duplicate key update"])) {
            // Check for "INSERT ... ON DUPLICATE KEY UPDATE"
            // If value is an array, then build: col_name=expr, col_name2=expr2, ...
            // Otherwise just append the value.
            $onDuplicateKeyUpdate = "ON DUPLICATE KEY UPDATE ";
            if(is_array($options["on duplicate key update"])) {
                $onDuplicateKeyUpdate .= implode(", ", $options["on duplicate key update"]);
            }
            else {
                $onDuplicateKeyUpdate .= $options["on duplicate key update"];
            }
        }

        // Refer to first and second cases above.
        if(isset($options["select"]) == false) {
            return trim(sprintf($query, $tableName, $fields, $binds, $onDuplicateKeyUpdate));
        }
        else {
            return trim(sprintf($query, $tableName, $select));
        }
    }


    /**
     * Creates new records.
     *
     * @param $data
     * @param $options
     */
    public function create($data, $options = array()) {
        Op::clearBinds();

        // Build query
        $query = self::insertQuery($this->model, $data, $options);

        // Execute query with prepared statement
        try {
            $stmt = self::$dbh->prepare($query);
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
     * and http://dev.mysql.com/doc/refman/5.0/en/join.html
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
        // Non join query
        // 1st %s: fields list
        // 2nd %s: table name
        // 3rd %s: where condition
        // 4th %s: grouping condition
        // 5th %s: having condition
        // 6th %s: ordering
        // 7th %s: limit
        $query = "SELECT %s FROM `%s` %s%s%s%s%s";

        // Check for join key and build special query if it has been found.
        if(isset($options["join"])) {
            // 1st %s: fields list
            // 2nd %s: table name
            // 3rd %s: join statement
            // 4rd %s: where condition
            // 5th %s: grouping condition
            // 6th %s: having condition
            // 7th %s: ordering
            // 8th %s: limit
            $query = "SELECT %s FROM `%s` %s%s%s%s%s%s";

            // Set join types, if not set then defaults to natural join
            if(isset($options["join"]["type"])) {
                $joinType = $options["join"]["type"]." ";
            }
            else {
                $joinType = "JOIN ";
            }

            // join tables (table factor). Should be inside an array.
            // build something like this (foo, bar, baz)
            if(is_array($options["join"]["tables"])) {
                $joinTables = "(".implode(", ", $options["join"]["tables"]).") ";
                $joinTables = strtolower($joinTables);
                $joinTables = preg_replace("/([\w0-9_]+)/", "`$1`", $joinTables);
            }
            else {
                // otherwise just use the value
                $joinTables = $options["join"]["tables"]." ";
            }

            // Join conditions
            if(isset($options["join"]["conditions"])) {
                $tmp = $options["join"]["conditions"];
                $joinConditions = "ON $tmp ";
            }
            else {
                $joinConditions = "";
            }

            // @todo: index hint
            if(isset($options["join"]["index"])) {
            }
            else {
            }

            $join = $joinType . $joinTables . $joinConditions;
        }

        // Pluralize model name
        $tableName = Inflector::tableize($model);

        // Build query
        // 1. Build (field1, field2, ..) and (?, ?, ..)
        if(isset($options["fields"])) {
            $fields = implode(", ", $options["fields"]);
            $fields = strtolower($fields);
            $fields = preg_replace("/([\w0-9_]+)/", "`$1`", $fields);
        }
        else {
            $fields = "*";
        }

        // Build WHERE condition
        if(isset($options["conditions"])) {
            $conditions = "WHERE ".$options["conditions"]." ";
        }
        else {
            $conditions = "";
        }

        // Build grouping
        if(isset($options["group"])) {
            $group = "GROUP BY `" . $options["group"] . "` ";
        }
        else {
            $group = "";
        }

        // Build having clause
        if(isset($options["having"])) {
            $having = "HAVING " . $options["having"] . " ";
        }
        else {
            $having = "";
        }

        // Build order
        if(isset($options["order"])) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        if(isset($options["limit"])) {
            $limit = "LIMIT :core_query_limit";
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }
        else {
            $limit = "";
        }

        if(isset($options["join"])) {
            // @todo
            return trim(sprintf($query, $fields, $tableName, $join, $conditions, $group, $having, $order, $limit));
        }
        else {
            return trim(sprintf($query, $fields, $tableName, $conditions, $group, $having, $order, $limit));
        }
    }


    /**
     * Selects records from database.
     *
     * @param $data
     * @param $options
     */
    public function read($data, $options = array()) {
        Op::clearBinds();

        // Build query
        $query = self::selectQuery($this->model, $data, $options);

        // Execute query with prepared statement
        try {
            $stmt = self::$dbh->prepare($query);
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
        $query = "UPDATE `%s` SET %s%s%s%s";

        // Pluralize model name
        $tableName = Inflector::tableize($model);

        // Build key-value pairs
        $sets = "";
        $count = count($data);
        $i = 0;
        foreach((array) $data as $key=>$value) {
            if($i < $count-1) {
                $sets .= "`$key` = :$key, ";
            }
            else {
                $sets .= "`$key` = :$key ";
            }
            ++$i;
        }

        // Build condition
        if(isset($options["conditions"])) {
            $conditions = "WHERE ".$options["conditions"]." ";
        }
        else {
            $conditions = "";
        }

        // Build order
        if(isset($options["order"])) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        if(isset($options["limit"])) {
            $limit = "LIMIT :core_query_limit";
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }
        else {
            $limit = "";
        }

        return trim(sprintf($query, $tableName, $sets, $conditions, $order, $limit));
    }


    /**
     * Updates records.
     *
     * @param $data
     * @param $options
     */
    public function update($data, $options = array()) {
        Op::clearBinds();

        // Build query
        $query = self::updateQuery($this->model, $data, $options);

        // Execute query with prepared statement
        try {
            $stmt = self::$dbh->prepare($query);
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
        $query = "DELETE FROM `%s` %s%s%s";

        // Pluralize model name
        $tableName = Inflector::tableize($model);

        // Build condition
        if(isset($options["conditions"])) {
            $conditions = "WHERE ".$options["conditions"]." ";
        }
        else {
            $conditions = "";
        }

        // Build order
        if(isset($options["order"])) {
            $order = "ORDER BY " . $options["order"] . " ";
        }
        else {
            $order = "";
        }

        // Build limit
        if(isset($options["limit"])) {
            $limit = "LIMIT :core_query_limit";
            Op::setBind("core_query_limit", (int) $options["limit"]);
        }
        else {
            $limit = "";
        }

        return trim(sprintf($query, $tableName, $conditions, $order, $limit));
    }


    /**
     * Deletes records from database.
     *
     * @param $options
     */
    public function delete($options = array()) {
        Op::clearBinds();

        // Build query
        $query = self::deleteQuery($this->model, $options);

        // Execute query with prepared statement
        try {
            $stmt = self::$dbh->prepare($query);
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