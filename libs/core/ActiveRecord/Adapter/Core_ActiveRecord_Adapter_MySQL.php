<?php

class Core_ActiveRecord_Adapter_MySQL extends Core_ActiveRecord_Adapter {

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
        $config = Core_Config::get("database");

        if(Core_Config::get("debug") == true) {
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
     * Builds insert query (prepared statement).
     * http://dev.mysql.com/doc/refman/5.0/en/insert.html
     *
     * @param $data     Data to insert.
     * @param $options  Options.
     * @return string   SQL Query (prepared statement).
     */
    public function insertQuery($data, $options) {
        $query = "INSERT INTO %s(%s) VALUES(%s) %s";

        // Pluralize model name
        $tableName = Core_Inflector::tableize($this->model);

        // Build field names based on $data keys
        $fields = implode(", ", array_keys($data));
        $fields = strtolower($fields);

        // Statements are bound with ? characters
        $dataCount = count($data);
        $binds = implode(", ", array_fill(0, $dataCount, "?"));

        // Work on the passed options
        $onDuplicateKeyUpdate = "";
        foreach($options as $key=>$value) {
            // Check for "on duplicate key update"
            // If value is an array, then build: col_name=expr, col_name2=expr2, ...
            // Otherwise just append the value.
            if( strtoupper($key) == "ON DUPLICATE KEY UPDATE") {
                $onDuplicateKeyUpdate = "ON DUPLICATE KEY UPDATE ";
                if( is_array($value) ) {
                    $onDuplicateKeyUpdate .= implode(", ", $value);
                }
                else {
                    $onDuplicateKeyUpdate .= $value;
                }
            }
        }

        $query = sprintf($query, $tableName, $fields, $binds, $onDuplicateKeyUpdate);
        return $query;
    }


    /**
     * @param $data
     * @param $options
     */
    public function create($data, $options) {
        // Build query
        $query = $this->insertQuery($data, $options);

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
            throw new ModelDriverException();
        }
    }


    /**
     * @param $data
     * @param $options
     * @return string
     */
    public function selectQuery($data, $options) {
        // Build query
        // 1. Build (field1, field2, ..) and (?, ?, ..)
        if( isset($options["fields"]) ) {
            $fields = "(" . implode(",", $options["fields"]) . ")";
            $questions = array_fill(0, count($options["fields"]), "?");
            $binds = "VALUES(" . implode(",", $questions) . ")";
        }
        else {
            $fields = "";
            $questions = array_fill(0, count($data), "?");
            $binds = "VALUES(" . implode(",", $questions) . ")";
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
     * @param $data
     * @param $options
     */
    public function read($data, $options) {
    }


    /**
     * @param $data
     * @param $options
     * @return string
     */
    public function updateQuery($data, $options) {
        return "";
    }


    /**
     * @param $data
     * @param $options
     */
    public function update($data, $options) {

    }


    /**
     * @param $data
     * @param $options
     * @return string
     */
    public function deleteQuery($data, $options) {
        return "";
    }


    /**
     * @param $data
     * @param $options
     */
    public function delete($data, $options) {

    }

}

?>
