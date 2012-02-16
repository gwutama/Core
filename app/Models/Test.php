<?php

namespace Models;
use \Core\ActiveRecord\Operator\MySQL as Op;

class Test extends \Core\ActiveRecord\Model {
    protected $adapter = "MySQL";

    protected $hasOne = array(
        "Lorem",
        array(
            "model" => "Foobar",
            "reference" => "test_id"
        )
    );

    protected $hasMany = array("Many");

    protected $belongsTo = array("Blah");

    protected $validations = array(
        "name" => array(),
        "foo" => array(),
        "bar" => array()
    );

    public function getSomething() {
        return $this->find(array(
            "conditions" => Op::eq("foo", "bar"))
        );
    }
}

?>