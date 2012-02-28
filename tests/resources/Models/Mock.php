<?php

namespace Models;
use Core\ActiveRecord\Operator\MySQL as Op;

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Model.php';

class Mock extends \Core\ActiveRecord\Model {

    public static $primaryKey = "id";

    public static $hasOne = array("Single");

    public static $fields = array(
        "id" => array(
            "type" => "integer",
            "null" => false
        ),
        "field1" => array(
            "type" => "string",
            "null" => true
        ),
        "field2" => array(
            "type" => "string",
            "null" => false
        ),
        "field3" => array(
            "type" => "string",
            "null" => false
        )
    );
}

?>