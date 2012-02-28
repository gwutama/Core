<?php

namespace Models;

class SimpleMock extends \Core\ActiveRecord\Model {

    public static $primaryKey = "id";

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