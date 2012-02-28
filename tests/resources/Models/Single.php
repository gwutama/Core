<?php

namespace Models;
use Core\ActiveRecord\Operator\MySQL as Op;

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Model.php';

class Single extends \Core\ActiveRecord\Model {
    public static $fields = array(
        "id" => array(
            "type" => "integer",
            "null" => false
        ),
        "field" => array(
            "type" => "string",
            "null" => true
        ),
        "mocks_id" => array(
            "type" => "integer",
            "null" => false
        )
    );
}

?>