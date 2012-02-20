<?php

namespace Models;
use \Core\ActiveRecord\Operator\MySQL as Op;

class Test extends \Core\ActiveRecord\Model {

    protected $primaryKey = "id"; // defaults to id. can be changed here.

    protected $hasOne = array(
        "Lorem", // references to lorems.tests_id
        array( // references to foobars.testid
            "model" => "Foobar",
            "reference" => "testid"
        )
    );

    protected $hasMany = array("Many");

    protected $belongsTo = array("Blah");


    public function getSomething() {
        return $this->findAll(array(
            "conditions" => Op::eq("foo", "bar")
        ));
    }

    public function validateName($name) {
        if(strlen($name) < 5) {
            throw new ActiveRecordModelValidationException("Name must be at least 5 characters long.");
        }
    }
}

?>