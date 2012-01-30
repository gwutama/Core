<?php

namespace Core\ActiveRecord\Adapter;

use \Core\ActiveRecord\Adapter as Adapter;

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
    public static function insertQuery($model, $data, $options) {
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
        $fields = implode(", ", array_keys($data));
        $fields = strtolower($fields);

        // Statements are bound with ? characters
        $dataCount = count($data);
        $binds = implode(", ", array_fill(0, $dataCount, "?"));

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
            return sprintf($query, $tableName, $fields, $binds, $onDuplicateKeyUpdate);
        }
        else {
            return sprintf($query, $tableName, $select);
        }
    }


    /**
     * Gets executed before inserting new records.
     *
     * @param $data
     * @param $options
     */
    public function beforeCreate($data, $options) {
    }


    /**
     * Creates new records.
     *
     * @param $data
     * @param $options
     */
    public function create($data, $options) {
        // Build query
        $query = self::insertQuery($this->model, $data, $options);

        // Execute query with prepared statement
        try {
            $stmt = $this->dbh->prepare($query);
            // bind parameters
            for($i = 1; $i < count($data); ++$i) {
                $stmt->bindParam($i, $data[$i]);
            }
            $stmt->execute();
        }
        catch(PDOException $e) {
            throw new ActiveRecordQueryException();
        }
    }


    /**
     * Builds select query.
     *
     * @param $data
     * @param $options
     * @return string
     */
    public static function selectQuery($model, $options) {
        // Build query
        // 1. Build (field1, field2, ..) and (?, ?, ..)
        if( isset($options["fields"]) ) {
            $fields = implode(",", $options["fields"]);
        }
        else {
            $fields = "*";
        }

        // Build order
        if( isset($options["order"]) ) {
            $order = "ORDER BY " . $options["order"][0] . " ";
        }

        // Build limit
        if( isset($options["limit"]) ) {
            $limit = "LIMIT " . $options["limit"];
        }
        else {
            $limit = "";
        }

        return "";
    }


    /**
     * Gets executed before selecting records.
     *
     * @param $data
     * @param $options
     */
    public function beforeRead($data, $options) {
    }


    /**
     * Selects records from database.
     *
     * @param $data
     * @param $options
     */
    public function read($data, $options) {
    }


    /**
     * Builds update query.
     *
     * @param $data
     * @param $options
     * @return string
     */
    public static function updateQuery($model, $data, $options) {
        return "";
    }


    /**
     * Gets executed before updating records.
     *
     * @param $data
     * @param $options
     */
    public function beforeUpdate($data, $options) {
    }


    /**
     * Updates records.
     *
     * @param $data
     * @param $options
     */
    public function update($data, $options) {
    }


    /**
     * Builds delete query.
     *
     * @param $data
     * @param $options
     * @return string
     */
    public static function deleteQuery($model, $data, $options) {
        return "";
    }


    /**
     * Gets executed before deleting records.
     *
     * @param $data
     * @param $options
     */
    public function beforeDelete($data, $options) {
    }


    /**
     * Deletes records from database.
     *
     * @param $data
     * @param $options
     */
    public function delete($data, $options) {
    }

}

?>
