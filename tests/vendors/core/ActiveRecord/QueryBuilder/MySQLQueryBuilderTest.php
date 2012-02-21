<?php

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Adapter.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Adapter\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\QueryBuilder.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\QueryBuilder\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\exceptions.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\Inflector.php';

use Core\ActiveRecord\QueryBuilder\MySQL as Builder;
use Core\ActiveRecord\Operator\MySQL as Op;

class MySQLQueryBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testInsert()
    {
        $b = new Builder("Model");
        $q = $b->insert(array("foo" => "bar"));
        $this->assertEquals("INSERT INTO `models`(`foo`) VALUES(:foo)", $q);

        $b = new Builder("Person");
        $q = $b->insert(array("foo" => "bar", "baz" => "blah"));
        $this->assertEquals("INSERT INTO `people`(`foo`, `baz`) VALUES(:foo, :baz)", $q);

        $b = new Builder("Student");
        $q = $b->insert(array("foo" => "bar", "baz" => "blah"), array(
            "on duplicate key update" => "`hello` = 'world'"
        ));
        $this->assertEquals("INSERT INTO `students`(`foo`, `baz`) VALUES(:foo, :baz) ON DUPLICATE KEY UPDATE `hello` = 'world'", $q);
    }


    public function testDelete()
    {
        $b = new Builder("Model");
        $q = $b->delete();
        $this->assertEquals("DELETE FROM `models`", $q);

        $b = new Builder("Model");
        $q = $b->delete(array(
            "conditions" => Op::eq("foo", "bar")
        ));
        $this->assertEquals("DELETE FROM `models` WHERE `foo` = :foo", $q);

        $b = new Builder("Model");
        $q = $b->delete(array(
            "conditions" => Op::eq("id", 42)
        ));
        $this->assertEquals("DELETE FROM `models` WHERE `id` = :id", $q);

        $b = new Builder("Stuff");
        $q = $b->delete(array(
            "conditions" => Op::bAnd(
                Op::eq("foo", "bar"),
                Op::eq("baz", "blah")
            )
        ));
        $this->assertEquals("DELETE FROM `stuffs` WHERE (`foo` = :foo AND `baz` = :baz)", $q);

        $b = new Builder("Model");
        $q = $b->delete(array(
            "conditions" => Op::bOr(
                Op::eq("foo", "bar"),
                Op::eq("baz", "blah")
            )
        ));
        $this->assertEquals("DELETE FROM `models` WHERE (`foo` = :foo OR `baz` = :baz)", $q);

        $b = new Builder("Object");
        $q = $b->delete(
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

        $b = new Builder("Date");
        $q = $b->delete(array("limit" => 1));
        $this->assertEquals("DELETE FROM `dates` LIMIT :core_query_limit", $q);

        $b = new Builder("Test");
        $q = $b->delete(array("order" => "`id` DESC"));
        $this->assertEquals("DELETE FROM `tests` ORDER BY `id` DESC", $q);

        $b = new Builder("Person");
        $q = $b->delete(array("order" => "`id` DESC", "limit" => 42));
        $this->assertEquals("DELETE FROM `people` ORDER BY `id` DESC LIMIT :core_query_limit", $q);

        $b = new Builder("Person");
        $q = $b->delete(array("order" => "`id` DESC, `name` ASC", "limit" => 42));
        $this->assertEquals("DELETE FROM `people` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);
    }


    public function testUpdate()
    {
        $b = new Builder("Table");
        $q = $b->update(array(
            "foo" => "bar",
            "baz" => "blah"
        ));
        $this->assertEquals("UPDATE `tables` SET `foo` = :foo, `baz` = :baz", $q);

        $b = new Builder("Model");
        $q = $b->update(array(
           "foo" => "bar",
           "baz" => "blah"
        ), array(
            "conditions" => Op::eq("id", 42)
        ));
        $this->assertEquals("UPDATE `models` SET `foo` = :foo, `baz` = :baz WHERE `id` = :id", $q);

        $b = new Builder("Thing");
        $q = $b->update(array(
            "foo" => "bar",
            "baz" => "blah"
        ), array(
            "conditions" => Op::bOr(
                Op::eq("id", 42),
                Op::eq("hello", "world")
            )
        ));
        $this->assertEquals("UPDATE `things` SET `foo` = :foo, `baz` = :baz WHERE (`id` = :id OR `hello` = :hello)", $q);

        $b = new Builder("Table");
        $q = $b->update(array(
            "foo" => "bar",
            "baz" => "blah"
        ), array(
            "limit" => 3
        ));
        $this->assertEquals("UPDATE `tables` SET `foo` = :foo, `baz` = :baz LIMIT :core_query_limit", $q);

        $b = new Builder("Date");
        $q = $b->update(array("foo" => "bar"), array("limit" => 1));
        $this->assertEquals("UPDATE `dates` SET `foo` = :foo LIMIT :core_query_limit", $q);

        $b = new Builder("Test");
        $q = $b->update(array("foo" => "bar"), array("order" => "`id` DESC"));
        $this->assertEquals("UPDATE `tests` SET `foo` = :foo ORDER BY `id` DESC", $q);

        $b = new Builder("Person");
        $q = $b->update(array("foo" => "bar"), array("order" => "`id` DESC", "limit" => 42));
        $this->assertEquals("UPDATE `people` SET `foo` = :foo ORDER BY `id` DESC LIMIT :core_query_limit", $q);

        $b = new Builder("Person");
        $q = $b->update(array("foo" => "bar"), array("order" => "`id` DESC, `name` ASC", "limit" => 42));
        $this->assertEquals("UPDATE `people` SET `foo` = :foo ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);
    }


    public function testSelect()
    {
        $b = new Builder("Cake");
        $q = $b->select();
        $this->assertEquals("SELECT * FROM `cakes`", $q);

        $b = new Builder("House");
        $q = $b->select(array("conditions" => Op::eq("id", 42)));
        $this->assertEquals("SELECT * FROM `houses` WHERE `id` = :id", $q);

        $b = new Builder("House");
        $q = $b->select(array(
            "conditions" => Op::bOr(
                Op::eq("id", 42),
                Op::eq("foo", "bar")
            )
        ));
        $this->assertEquals("SELECT * FROM `houses` WHERE (`id` = :id OR `foo` = :foo)", $q);

        $b = new Builder("Movie");
        $q = $b->select(array(
            "fields" => array("id", "name", "lorem", "ipsum"),
            "conditions" => Op::bAnd(
                Op::eq("hello", "world"),
                Op::eq("foo", "bar")
            )
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `movies` WHERE (`hello` = :hello AND `foo` = :foo)", $q);

        $b = new Builder("Person");
        $q = $b->select(array(
            "fields" => array("id", "name", "lorem", "ipsum"),
            "conditions" => Op::bAnd(
                Op::eq("hello", "world"),
                Op::eq("foo", "bar")
            ),
            "limit" => 3
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `people` WHERE (`hello` = :hello AND `foo` = :foo) LIMIT :core_query_limit", $q);

        $b = new Builder("Person");
        $q = $b->select(array(
            "fields" => array("id", "name", "lorem", "ipsum"),
            "limit" => 3
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `people` LIMIT :core_query_limit", $q);

        $b = new Builder("Address");
        $q = $b->select(array(
            "fields" => array("id", "name", "lorem"),
            "limit" => 3,
            "offset" => 5
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem` FROM `addresses` LIMIT :core_query_limit OFFSET :core_query_offset", $q);

        $b = new Builder("Table");
        $q = $b->select(array(
            "fields" => array("id", "name", "lorem"),
            "offset" => 5 // offset won't work without limit.
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem` FROM `tables`", $q);

        $b = new Builder("Name");
        $q = $b->select(array(
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `names` ORDER BY `id` DESC", $q);

        $b = new Builder("Man");
        $q = $b->select(array(
            "order" => "`id` DESC, `name` ASC",
            "limit" => 42
        ));
        $this->assertEquals("SELECT * FROM `men` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);

        $b = new Builder("Site");
        $q = $b->select(array(
            "order" => "`id` DESC, `name` ASC",
            "limit" => 42
        ));
        $this->assertEquals("SELECT * FROM `sites` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);

        $b = new Builder("Site");
        $q = $b->select(array(
            "order" => "`id` DESC, `name` ASC",
            "limit" => 42,
            "offset" => 5
        ));
        $this->assertEquals("SELECT * FROM `sites` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit OFFSET :core_query_offset", $q);

        $b = new Builder("Appointment");
        $q = $b->select(array(
            "group" => "year"
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year`", $q);

        $b = new Builder("Appointment");
        $q = $b->select(array(
            "group" => "year",
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year` ORDER BY `id` DESC", $q);

        $b = new Builder("Appointment");
        $q = $b->select(array(
            "group" => "year",
            "order" => "`id` DESC",
            "limit" => 10
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year` ORDER BY `id` DESC LIMIT :core_query_limit", $q);

        $b = new Builder("Appointment");
        $q = $b->select(array(
            "having" => Op::eq("date", "NOW()")
        ));
        $this->assertEquals("SELECT * FROM `appointments` HAVING `date` = :date", $q);

        $b = new Builder("Appointment");
        $q = $b->select(array(
            "having" => Op::eq("date", "NOW()"),
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `appointments` HAVING `date` = :date ORDER BY `id` DESC", $q);

        $b = new Builder("Test");
        $q = $b->select(array(
            "join" => array(
                "tables" => array("things"),
                "conditions" => "tests.id = things.tests_id"
            )
        ));
        $this->assertEquals("SELECT * FROM `tests` JOIN (`things`) ON tests.id = things.tests_id", $q);

        $b = new Builder("Family");
        $q = $b->select(array(
           "fields" => array("families.position", "families.meal"),
           "join" => array(
               "tables" => array("foods"),
               "conditions" => "families.position = foods.position",
               "type" => "LEFT JOIN"
           )
        ));
        $this->assertEquals("SELECT `families`.`position`, `families`.`meal` FROM `families` LEFT JOIN (`foods`)".
                                " ON families.position = foods.position", $q);

        $b = new Builder("Family");
        $q = $b->select(array(
            "fields" => array("families.position", "families.meal"),
            "join" => array(
                "tables" => array("foods"),
                "conditions" => "families.position = foods.position",
                "type" => "LEFT JOIN"
            ),
            "conditions" => Op::eq("families.position", "father")
        ));
        $this->assertEquals("SELECT `families`.`position`, `families`.`meal` FROM `families` LEFT JOIN (`foods`)".
            " ON families.position = foods.position WHERE `families`.`position` = :families.position", $q);

        $b = new Builder("Family");
        $q = $b->select(array(
            "fields" => array("families.position", "families.meal"),
            "join" => array(
                "tables" => array("foods"),
                "conditions" => "families.position = foods.position",
                "type" => "LEFT JOIN"
            ),
            "conditions" => Op::eq("families.position", "father"),
            "limit" => 10,
            "offset" => 5
        ));
        $this->assertEquals("SELECT `families`.`position`, `families`.`meal` FROM `families` LEFT JOIN (`foods`)".
            " ON families.position = foods.position WHERE `families`.`position` = :families.position".
            " LIMIT :core_query_limit OFFSET :core_query_offset", $q);
    }
}

?>