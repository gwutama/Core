<?php

require_once 'vendors/Core/ActiveRecord/Adapter.php';
require_once 'vendors/Core/ActiveRecord/Adapter/MySQL.php';
require_once 'vendors/Core/ActiveRecord/Operatorable.php';
require_once 'vendors/Core/ActiveRecord/Operator/MySQL.php';
require_once 'vendors/Core/ActiveRecord/QueryBuilder.php';
require_once 'vendors/Core/ActiveRecord/QueryBuilder/MySQL.php';
require_once 'vendors/Core/exceptions.php';
require_once 'vendors/Core/Inflector.php';

use Core\ActiveRecord\QueryBuilder\MySQL as Builder;
use Core\ActiveRecord\Operator\MySQL as Op;

class MySQLQueryBuilderTest extends PHPUnit_Framework_TestCase
{
    public function testInsert()
    {
        $q = Builder::insert("models", array("foo" => "bar"));
        $this->assertEquals("INSERT INTO `models`(`foo`) VALUES(:foo)", $q);

        $q = Builder::insert("people", array("foo" => "bar", "baz" => "blah"));
        $this->assertEquals("INSERT INTO `people`(`foo`, `baz`) VALUES(:foo, :baz)", $q);

        $q = Builder::insert("students", array("foo" => "bar", "baz" => "blah"), array(
            "on duplicate key update" => "`hello` = 'world'"
        ));
        $this->assertEquals("INSERT INTO `students`(`foo`, `baz`) VALUES(:foo, :baz) ON DUPLICATE KEY UPDATE `hello` = 'world'", $q);
    }


    public function testDelete()
    {
        $q = Builder::delete("models");
        $this->assertEquals("DELETE FROM `models`", $q);

        $q = Builder::delete("models", array(
            "conditions" => Op::eq("foo", "bar")
        ));
        $this->assertEquals("DELETE FROM `models` WHERE `foo` = :foo", $q);

        $q = Builder::delete("models", array(
            "conditions" => Op::eq("id", 42)
        ));
        $this->assertEquals("DELETE FROM `models` WHERE `id` = :id", $q);

        $q = Builder::delete("stuffs", array(
            "conditions" => Op::bAnd(
                Op::eq("foo", "bar"),
                Op::eq("baz", "blah")
            )
        ));
        $this->assertEquals("DELETE FROM `stuffs` WHERE (`foo` = :foo AND `baz` = :baz)", $q);

        $q = Builder::delete("models", array(
            "conditions" => Op::bOr(
                Op::eq("foo", "bar"),
                Op::eq("baz", "blah")
            )
        ));
        $this->assertEquals("DELETE FROM `models` WHERE (`foo` = :foo OR `baz` = :baz)", $q);

        $q = Builder::delete("objects",
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

        $q = Builder::delete("dates", array("limit" => 1));
        $this->assertEquals("DELETE FROM `dates` LIMIT :core_query_limit", $q);

        $q = Builder::delete("tests", array("order" => "`id` DESC"));
        $this->assertEquals("DELETE FROM `tests` ORDER BY `id` DESC", $q);

        $q = Builder::delete("people", array("order" => "`id` DESC", "limit" => 42));
        $this->assertEquals("DELETE FROM `people` ORDER BY `id` DESC LIMIT :core_query_limit", $q);

        $q = Builder::delete("people", array("order" => "`id` DESC, `name` ASC", "limit" => 42));
        $this->assertEquals("DELETE FROM `people` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);
    }


    public function testUpdate()
    {
        $q = Builder::update("tables", array(
            "foo" => "bar",
            "baz" => "blah"
        ));
        $this->assertEquals("UPDATE `tables` SET `foo` = :foo, `baz` = :baz", $q);

        $q = Builder::update("models", array(
           "foo" => "bar",
           "baz" => "blah"
        ), array(
            "conditions" => Op::eq("id", 42)
        ));
        $this->assertEquals("UPDATE `models` SET `foo` = :foo, `baz` = :baz WHERE `id` = :id", $q);

        $q = Builder::update("things", array(
            "foo" => "bar",
            "baz" => "blah"
        ), array(
            "conditions" => Op::bOr(
                Op::eq("id", 42),
                Op::eq("hello", "world")
            )
        ));
        $this->assertEquals("UPDATE `things` SET `foo` = :foo, `baz` = :baz WHERE (`id` = :id OR `hello` = :hello)", $q);

        $q = Builder::update("tables", array(
            "foo" => "bar",
            "baz" => "blah"
        ), array(
            "limit" => 3
        ));
        $this->assertEquals("UPDATE `tables` SET `foo` = :foo, `baz` = :baz LIMIT :core_query_limit", $q);

        $q = Builder::update("dates", array("foo" => "bar"), array("limit" => 1));
        $this->assertEquals("UPDATE `dates` SET `foo` = :foo LIMIT :core_query_limit", $q);

        $q = Builder::update("tests", array("foo" => "bar"), array("order" => "`id` DESC"));
        $this->assertEquals("UPDATE `tests` SET `foo` = :foo ORDER BY `id` DESC", $q);

        $q = Builder::update("people", array("foo" => "bar"), array("order" => "`id` DESC", "limit" => 42));
        $this->assertEquals("UPDATE `people` SET `foo` = :foo ORDER BY `id` DESC LIMIT :core_query_limit", $q);

        $q = Builder::update("people", array("foo" => "bar"), array("order" => "`id` DESC, `name` ASC", "limit" => 42));
        $this->assertEquals("UPDATE `people` SET `foo` = :foo ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);
    }


    public function testSelect()
    {
        $q = Builder::select("cakes");
        $this->assertEquals("SELECT * FROM `cakes`", $q);

        $q = Builder::select("houses", array("conditions" => Op::eq("id", 42)));
        $this->assertEquals("SELECT * FROM `houses` WHERE `id` = :id", $q);

        $q = Builder::select("houses", array(
            "conditions" => Op::bOr(
                Op::eq("id", 42),
                Op::eq("foo", "bar")
            )
        ));
        $this->assertEquals("SELECT * FROM `houses` WHERE (`id` = :id OR `foo` = :foo)", $q);

        $q = Builder::select("movies", array(
            "fields" => array("id", "name", "lorem", "ipsum"),
            "conditions" => Op::bAnd(
                Op::eq("hello", "world"),
                Op::eq("foo", "bar")
            )
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `movies` WHERE (`hello` = :hello AND `foo` = :foo)", $q);

        $q = Builder::select("people", array(
            "fields" => array("id", "name", "lorem", "ipsum"),
            "conditions" => Op::bAnd(
                Op::eq("hello", "world"),
                Op::eq("foo", "bar")
            ),
            "limit" => 3
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `people` WHERE (`hello` = :hello AND `foo` = :foo) LIMIT :core_query_limit", $q);

        $q = Builder::select("people", array(
            "fields" => array("id", "name", "lorem", "ipsum"),
            "limit" => 3
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem`, `ipsum` FROM `people` LIMIT :core_query_limit", $q);

        $q = Builder::select("addresses", array(
            "fields" => array("id", "name", "lorem"),
            "limit" => 3,
            "offset" => 5
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem` FROM `addresses` LIMIT :core_query_limit OFFSET :core_query_offset", $q);

        $q = Builder::select("tables", array(
            "fields" => array("id", "name", "lorem"),
            "offset" => 5 // offset won't work without limit.
        ));
        $this->assertEquals("SELECT `id`, `name`, `lorem` FROM `tables`", $q);

        $q = Builder::select("names", array(
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `names` ORDER BY `id` DESC", $q);

        $q = Builder::select("men", array(
            "order" => "`id` DESC, `name` ASC",
            "limit" => 42
        ));
        $this->assertEquals("SELECT * FROM `men` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);

        $q = Builder::select("sites", array(
            "order" => "`id` DESC, `name` ASC",
            "limit" => 42
        ));
        $this->assertEquals("SELECT * FROM `sites` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit", $q);

        $q = Builder::select("sites", array(
            "order" => "`id` DESC, `name` ASC",
            "limit" => 42,
            "offset" => 5
        ));
        $this->assertEquals("SELECT * FROM `sites` ORDER BY `id` DESC, `name` ASC LIMIT :core_query_limit OFFSET :core_query_offset", $q);

        $q = Builder::select("appointments", array(
            "group" => "year"
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year`", $q);

        $q = Builder::select("appointments", array(
            "group" => "year",
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year` ORDER BY `id` DESC", $q);

        $q = Builder::select("appointments", array(
            "group" => "year",
            "order" => "`id` DESC",
            "limit" => 10
        ));
        $this->assertEquals("SELECT * FROM `appointments` GROUP BY `year` ORDER BY `id` DESC LIMIT :core_query_limit", $q);

        $q = Builder::select("appointments", array(
            "having" => Op::eq("date", "NOW()")
        ));
        $this->assertEquals("SELECT * FROM `appointments` HAVING `date` = :date", $q);

        $q = Builder::select("appointments", array(
            "having" => Op::eq("date", "NOW()"),
            "order" => "`id` DESC"
        ));
        $this->assertEquals("SELECT * FROM `appointments` HAVING `date` = :date ORDER BY `id` DESC", $q);
    }


    public function testSimpleJoins() {
        $q = Builder::select("tests", array(
            "join" => array(
                "tables" => array("things"),
                "conditions" => "tests.id = things.tests_id"
            )
        ));
        $this->assertEquals("SELECT * FROM `tests` JOIN (`things`) ON (tests.id = things.tests_id)", $q);

        $q = Builder::select("families", array(
            "fields" => array("families.position", "families.meal"),
            "join" => array(
                "tables" => array("foods"),
                "conditions" => "families.position = foods.position",
                "type" => "LEFT JOIN"
            )
        ));
        $this->assertEquals("SELECT `families`.`position`, `families`.`meal` FROM `families` LEFT JOIN (`foods`)".
            " ON (families.position = foods.position)", $q);

        $q = Builder::select("families", array(
            "fields" => array("families.position", "families.meal"),
            "join" => array(
                "tables" => array("foods"),
                "conditions" => "families.position = foods.position",
                "type" => "LEFT JOIN"
            ),
            "conditions" => Op::eq("families.position", "father")
        ));
        $this->assertEquals("SELECT `families`.`position`, `families`.`meal` FROM `families` LEFT JOIN (`foods`)".
            " ON (families.position = foods.position) WHERE `families`.`position` = :families_position", $q);

        $q = Builder::select("families", array(
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
            " ON (families.position = foods.position) WHERE `families`.`position` = :families_position".
            " LIMIT :core_query_limit OFFSET :core_query_offset", $q);
    }


    public function testMultipleJoins() {
        $q = Builder::select("tests", array(
            "join" => array(
                "tables" => array("things", "foobars"),
                "conditions" => "tests.id = things.tests_id AND things.tests_id = foobars.things_id"
            )
        ));
        $this->assertEquals("SELECT * FROM `tests` JOIN (`things`, `foobars`) ON".
            " (tests.id = things.tests_id AND things.tests_id = foobars.things_id)", $q);

        $q = Builder::select("families", array(
            "fields" => array("families.position", "families.meal"),
            "join" => array(
                "tables" => array("foods", "restaurants"),
                "conditions" => "families.position = foods.position AND foods.restaurant = restaurants.id",
                "type" => "LEFT JOIN"
            ),
            "conditions" => Op::eq("families.position", "father"),
            "limit" => 10,
            "offset" => 5
        ));
        $this->assertEquals("SELECT `families`.`position`, `families`.`meal` FROM `families`".
            " LEFT JOIN (`foods`, `restaurants`)".
            " ON (families.position = foods.position AND foods.restaurant = restaurants.id)".
            " WHERE `families`.`position` = :families_position".
            " LIMIT :core_query_limit OFFSET :core_query_offset", $q);
    }
}

?>