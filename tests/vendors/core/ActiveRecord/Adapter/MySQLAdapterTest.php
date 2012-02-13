<?php

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Adapter.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Adapter\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\exceptions.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\Inflector.php';

use Core\ActiveRecord\Adapter\MySQL;
use Core\ActiveRecord\Operator\MySQL as Op;

class MySQLAdapterTest extends PHPUnit_Framework_TestCase
{
    public function testInsertQuery()
    {
        $q = MySQL::insertQuery("Model", array("foo" => "bar"));
        $this->assertEquals("INSERT INTO `models`(`foo`) VALUES(:foo)", $q);
        $this->assertEquals(array(":foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::insertQuery("Person", array("foo" => "bar", "baz" => "blah"));
        $this->assertEquals("INSERT INTO `people`(`foo`, `baz`) VALUES(:foo, :baz)", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::insertQuery("Student", array("foo" => "bar", "baz" => "blah"), array(
            "on duplicate key update" => "`hello` = 'world'"
        ));
        $this->assertEquals("INSERT INTO `students`(`foo`, `baz`) VALUES(:foo, :baz) ON DUPLICATE KEY UPDATE `hello` = 'world'", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah"), Op::getBinds());
        Op::clearBinds();
    }


    public function testDeleteQuery()
    {
        $q = MySQL::deleteQuery("Model");
        $this->assertEquals("DELETE FROM `models`", $q);
        $this->assertEquals(array(), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Model",
            array("conditions" => Op::eq("foo", "bar")
        ));
        $this->assertEquals("DELETE FROM `models` WHERE `foo` = :foo", $q);
        $this->assertEquals(array(":foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Model",
            array("conditions" => Op::eq("id", 42)
        ));
        $this->assertEquals("DELETE FROM `models` WHERE `id` = :id", $q);
        $this->assertEquals(array(":id" => 42), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Stuff",
            array("conditions" => Op::bAnd(
                Op::eq("foo", "bar"),
                Op::eq("baz", "blah")
            )
        ));
        $this->assertEquals("DELETE FROM `stuffs` WHERE (`foo` = :foo AND `baz` = :baz)", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Model",
            array("conditions" => Op::bOr(
                Op::eq("foo", "bar"),
                Op::eq("baz", "blah")
            )
        ));
        $this->assertEquals("DELETE FROM `models` WHERE (`foo` = :foo OR `baz` = :baz)", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Object",
            array("conditions" =>
                Op::bOr(
                    Op::bAnd(
                        Op::eq("foo", "bar"),
                        Op::eq("baz", "blah")
                    ),
                    Op::eq("id", 42)
                )
            ));
        $this->assertEquals("DELETE FROM `objects` WHERE ((`foo` = :foo AND `baz` = :baz) OR `id` = :id)", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah", ":id" => 42), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Date", array("limit" => 1));
        $this->assertEquals("DELETE FROM `dates` LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":core_query_limit" => 1), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Test", array("order" => "`id` DESC"));
        $this->assertEquals("DELETE FROM `tests` ORDER BY `id` DESC", $q);
        $this->assertEquals(array(), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Person", array("order" => "`id` DESC", "limit" => 42));
        $this->assertEquals("DELETE FROM `people` ORDER BY `id` DESC LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":core_query_limit" => 42), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::deleteQuery("Person", array("order" => "`id` DESC, `name` ASC", "limit" => 42));
        $this->assertEquals("DELETE FROM `people` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":core_query_limit" => 42), Op::getBinds());
        Op::clearBinds();
    }


    public function testUpdateQuery()
    {
        $q = MySQL::updateQuery("Table", array(
            "foo" => "bar",
            "baz" => "blah"
        ));
        $this->assertEquals("UPDATE `tables` SET `foo` = :foo, `baz` = :baz", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::updateQuery("Model", array(
           "foo" => "bar",
           "baz" => "blah"
        ), array(
            "conditions" => Op::eq("id", 42)
        ));
        $this->assertEquals("UPDATE `models` SET `foo` = :foo, `baz` = :baz WHERE `id` = :id", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah", ":id" => 42), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::updateQuery("Thing", array(
            "foo" => "bar",
            "baz" => "blah"
        ), array(
            "conditions" => Op::bOr(
                Op::eq("id", 42),
                Op::eq("hello", "world")
            )
        ));
        $this->assertEquals("UPDATE `things` SET `foo` = :foo, `baz` = :baz WHERE (`id` = :id OR `hello` = :hello)", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah", ":id" => 42, ":hello" => "world"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::updateQuery("Table", array(
            "foo" => "bar",
            "baz" => "blah"
        ), array(
            "limit" => 3
        ));
        $this->assertEquals("UPDATE `tables` SET `foo` = :foo, `baz` = :baz LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah", ":core_query_limit" => 3), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::updateQuery("Date", array("foo" => "bar"), array("limit" => 1));
        $this->assertEquals("UPDATE `dates` SET `foo` = :foo LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":foo" => "bar", ":core_query_limit" => 1), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::updateQuery("Test", array("foo" => "bar"), array("order" => "`id` DESC"));
        $this->assertEquals("UPDATE `tests` SET `foo` = :foo ORDER BY `id` DESC", $q);
        $this->assertEquals(array(":foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::updateQuery("Person", array("foo" => "bar"), array("order" => "`id` DESC", "limit" => 42));
        $this->assertEquals("UPDATE `people` SET `foo` = :foo ORDER BY `id` DESC LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":foo" => "bar", ":core_query_limit" => 42), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::updateQuery("Person", array("foo" => "bar"), array("order" => "`id` DESC, `name` ASC", "limit" => 42));
        $this->assertEquals("UPDATE `people` SET `foo` = :foo ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":foo" => "bar", ":core_query_limit" => 42), Op::getBinds());
        Op::clearBinds();
    }


    public function testSelectQuery()
    {
        $q = MySQL::selectQuery("Cake");
        $this->assertEquals("SELECT * FROM `cakes`", $q);

        $q = MySQL::selectQuery("House", array("conditions" => Op::eq("id", 42)));
        $this->assertEquals("SELECT * FROM `houses` WHERE `id` = :id", $q);
        $this->assertEquals(array(":id" => 42), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("House", array(
            "conditions" => Op::bOr(
                Op::eq("id", 42),
                Op::eq("foo", "bar")
            )
        ));
        $this->assertEquals("SELECT * FROM `houses` WHERE (`id` = :id OR `foo` = :foo)", $q);
        $this->assertEquals(array(":id" => 42, ":foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("Movie", array(
            "fields" => array("id, name, lorem, ipsum"),
            "conditions" => Op::bAnd(
                Op::eq("hello", "world"),
                Op::eq("foo", "bar")
            )
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `movies` WHERE (`hello` = :hello AND `foo` = :foo)", $q);
        $this->assertEquals(array(":hello" => "world", ":foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("Person", array(
            "fields" => array("id, name, lorem, ipsum"),
            "conditions" => Op::bAnd(
                Op::eq("hello", "world"),
                Op::eq("foo", "bar")
            ),
            "limit" => 3
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `people` WHERE (`hello` = :hello AND `foo` = :foo) LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":hello" => "world", ":foo" => "bar", ":core_query_limit" => 3), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("Person", array(
            "fields" => array("id, name, lorem, ipsum"),
            "limit" => 3
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `people` LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":core_query_limit" => 3), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("Name", array(
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `names` ORDER BY `id` DESC", $q);

        $q = MySQL::selectQuery("Man", array(
            "order" => "`id` DESC, `name` ASC",
            "limit" => 42
        ));
        $this->assertEquals("SELECT * FROM `men` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":core_query_limit" => 42), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("Site", array(
            "order" => "`id` DESC, `name` ASC",
            "limit" => 42
        ));
        $this->assertEquals("SELECT * FROM `sites` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":core_query_limit" => 42), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("Appointment", array(
            "group" => "year"
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year`", $q);

        $q = MySQL::selectQuery("Appointment", array(
            "group" => "year",
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year` ORDER BY `id` DESC", $q);

        $q = MySQL::selectQuery("Appointment", array(
            "group" => "year",
            "order" => "`id` DESC",
            "limit" => 10
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year` ORDER BY `id` DESC LIMIT :core_query_limit", $q);
        $this->assertEquals(array(":core_query_limit" => 10), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("Appointment", array(
            "having" => Op::eq("date", "NOW()")
        ));
        $this->assertEquals("SELECT * FROM `appointments` HAVING `date` = :date", $q);
        $this->assertEquals(array(":date" => "NOW()"), Op::getBinds());
        Op::clearBinds();

        $q = MySQL::selectQuery("Appointment", array(
            "having" => Op::eq("date", "NOW()"),
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `appointments` HAVING `date` = :date ORDER BY `id` DESC", $q);
        $this->assertEquals(array(":date" => "NOW()"), Op::getBinds());
        Op::clearBinds();
    }
}

?>