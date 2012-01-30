<?php

class ActiveRecord_Adapter_MySQL extends ActiveRecord_Adapter {

    private static $insertString = "INSERT INTO %s(%s) VALUES(%s)";

    private static $selectString = "SELECT %s FROM %s";

    private static $updateString = "UPDATE %s SET %s";

    private static $deleteString = "DELETE FROM %s %s";


    /**
     * Connects to mysql server.
     */
    public function connect() {
        try {

        }
        catch(PDOException $e) {
            throw new AdapterConnectionException();
        }
    }


    /**
     * Disconnects from mysql server.
     */
    public function disconnect() {

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
