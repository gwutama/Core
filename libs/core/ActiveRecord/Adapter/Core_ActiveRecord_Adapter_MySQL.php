<?php

class Core_ActiveRecord_Adapter_MySQL extends Core_ActiveRecord_Adapter {

    protected $dsn;
    protected $username;
    protected $password;
    protected $persistent;

    protected static $insertString = "INSERT INTO %s(%s) VALUES(%s)";
    protected static $selectString = "SELECT %s FROM %s";
    protected static $updateString = "UPDATE %s SET %s";
    protected static $deleteString = "DELETE FROM %s %s";


    /**
     * Connects to mysql server.
     */
    protected function connect() {
        $this->beforeConnect();
        try {
            $this->dbh = new PDO($this->dsn, $this->username, $this->password, array(
                PDO::ATTR_PERSISTENT => $this->persistent
            ));
        }
        catch(PDOException $e) {
            throw new AdapterConnectionException("Cannot connect to MySQL server.");
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
     *
     */
    public function create($data, $options) {
        try {
            $sql = "";
            $this->dbh->exec($sql);
        }
        catch(PDOException $e) {
            throw new ModelDriverException();
        }
    }


    /**
     *
     */
    public function read($data, $options) {

    }


    /**
     *
     */
    public function update($data, $options) {

    }


    /**
     *
     */
    public function delete($data, $options) {

    }
}

?>
