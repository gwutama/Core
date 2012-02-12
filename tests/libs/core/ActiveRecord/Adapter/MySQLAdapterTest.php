<?php

require_once 'C:\Users\Galuh Utama\workspace\Core\libs\Core\ActiveRecord\Adapter.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\libs\Core\ActiveRecord\Adapter\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\libs\Core\exceptions.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\libs\Core\ActiveRecord\Operator.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\libs\Core\ActiveRecord\Operator\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\libs\Core\Inflector.php';

use Core\ActiveRecord\Adapter\MySQL;
use Core\ActiveRecord\Operator\MySQL as Op;

class MySQLAdapterTest extends PHPUnit_Framework_TestCase
{
    public function testInsertQuery()
    {
        $q = MySQL::insertQuery("Model", array("foo" => "bar"));
        $this->assertEquals("INSERT INTO models(foo) VALUES(:foo)", $q);
        Op::clearBinds();

        $q = MySQL::insertQuery("Person", array("foo" => "bar", "baz" => "blah"));
        $this->assertEquals("INSERT INTO people(foo, baz) VALUES(:foo, :baz)", $q);
        Op::clearBinds();

        $q = MySQL::insertQuery("Student", array("foo" => "bar", "baz" => "blah"), array(
            "on duplicate key update" => array("hello" => "world")
        ));
        //$this->assertEquals("INSERT INTO people(foo, baz) VALUES(?, ?) ON DUPLICATE KEY UPDATE hello = 'world'", $q);
        Op::clearBinds();

    }


    public function testDeleteQuery()
    {
        $q = MySQL::deleteQuery("Model",
            array("conditions" => Op::eq("foo", "bar")
        ));
        $this->assertEquals("DELETE FROM models WHERE foo = :foo", $q);
        Op::clearBinds();

        $q = MySQL::deleteQuery("Model",
            array("conditions" => Op::eq("id", 42)
        ));
        $this->assertEquals("DELETE FROM models WHERE id = :id", $q);
        Op::clearBinds();

        $q = MySQL::deleteQuery("Model",
            array("conditions" => Op::bAnd(
                Op::eq("foo", "bar"),
                Op::eq("baz", "blah")
            )
        ));
        $this->assertEquals("DELETE FROM models WHERE (foo = :foo AND baz = :baz)", $q);
        Op::clearBinds();

        $q = MySQL::deleteQuery("Model",
            array("conditions" => Op::bOr(
                Op::eq("foo", "bar"),
                Op::eq("baz", "blah")
            )
        ));
        $this->assertEquals("DELETE FROM models WHERE (foo = :foo OR baz = :baz)", $q);
        Op::clearBinds();

        $q = MySQL::deleteQuery("Model",
            array("conditions" =>
                Op::bOr(
                    Op::bAnd(
                        Op::eq("foo", "bar"),
                        Op::eq("baz", "blah")
                    ),
                    Op::eq("id", 42)
                )
            ));
        $this->assertEquals("DELETE FROM models WHERE ((foo = :foo AND baz = :baz) OR id = :id)", $q);
        Op::clearBinds();
    }


    public function testUpdateQuery()
    {
        $q = MySQL::updateQuery("Model", array(
           "foo" => "bar",
           "baz" => "blah"
        ), array(
            "conditions" => Op::eq("id", 42)
        ));
        $this->assertEquals("UPDATE models SET foo = :foo, baz = :baz WHERE id = :id", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah", ":id" => 42), Op::getBinds());
        Op::clearBinds();
    }
}

?>