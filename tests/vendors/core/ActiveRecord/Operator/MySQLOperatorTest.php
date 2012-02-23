<?php

require_once 'vendors/Core/ActiveRecord/Operatorable.php';
require_once 'vendors/Core/ActiveRecord/Operator/MySQL.php';

use Core\ActiveRecord\Operator\MySQL as Op;

class MySQLOperatorTest extends PHPUnit_Framework_TestCase
{
    public function testSetBinds()
    {
        $op = new Op();
        $op->setBinds(array());
        $this->assertEquals(array(), $op->getBinds());
        $op->clearBinds();

        $op->setBinds(null);
        $this->assertEquals(array(), $op->getBinds());
        $op->clearBinds();

        $op->setBinds(array("foo" => "bar", "baz" => "blah", "hello" => "world"));
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah", ":hello" => "world"), $op->getBinds());
        $op->clearBinds();

        $op->setBinds(array("foo123" => "bar", "456baz" => "blah", "_hello" => "world", "test_" => 42));
        $this->assertEquals(array(":foo123" => "bar", ":456baz" => "blah", ":_hello" => "world", ":test_" => 42), $op->getBinds());
        $op->clearBinds();
    }

    /**
     * @expectedException Core\ActiveRecordOperatorException
     */
    public function testSetBindsException()
    {
        $op = new Op();
        $op->setBinds(array("foo"));
    }

    public function testClearBinds()
    {
        $op = new Op();
        $op->setBinds(array());
        $op->clearBinds();
        $this->assertEquals(array(), $op->getBinds());

        $op->setBinds(null);
        $op->clearBinds();
        $this->assertEquals(array(), $op->getBinds());

        $op->setBinds(array("foo" => "bar", "baz" => "blah", "hello" => "world"));
        $op->clearBinds();
        $this->assertEquals(array(), $op->getBinds());

        $op->setBinds(array("foo123" => "bar", "456baz" => "blah", "_hello" => "world", "test_" => 42));
        $op->clearBinds();
        $this->assertEquals(array(), $op->getBinds());
    }

    public function testAnd()
    {
        $op = new Op();
        $this->assertEquals("(1 AND 2)", $op->bAnd(1, 2));
        $this->assertEquals("(1 AND 2 AND 3)", $op->bAnd(1, 2, 3));
        $this->assertEquals("(1 AND 2 AND 3 AND 4)", $op->bAnd(1, 2, 3, 4));
        $this->assertEquals("1", $op->bAnd(1));
        $this->assertEquals("", $op->bAnd());

        $this->assertEquals("((1 AND 2) AND (3 AND 4))", $op->bAnd($op->bAnd(1, 2), $op->bAnd(3, 4)));
        $this->assertEquals("((1 AND 2 AND 3) AND (3 AND 4 AND 5))", $op->bAnd($op->bAnd(1, 2, 3), $op->bAnd(3, 4, 5)));
        $this->assertEquals("(((1 AND 2) AND (3 AND 4)) AND (4 AND 5))", $op->bAnd($op->bAnd($op->bAnd(1, 2), $op->bAnd(3, 4)), $op->bAnd(4, 5)));
    }

    public function testOr()
    {
        $op = new Op();
        $this->assertEquals("(1 OR 2)", $op->bOr(1, 2));
        $this->assertEquals("(1 OR 2 OR 3)", $op->bOr(1, 2, 3));
        $this->assertEquals("(1 OR 2 OR 3 OR 4)", $op->bOr(1, 2, 3, 4));
        $this->assertEquals("1", $op->bOr(1));
        $this->assertEquals("", $op->bOr());

        $this->assertEquals("((1 OR 2) OR (3 OR 4))", $op->bOr($op->bOr(1, 2), $op->bOr(3, 4)));
        $this->assertEquals("((1 OR 2 OR 3) OR (3 OR 4 OR 5))", $op->bOr($op->bOr(1, 2, 3), $op->bOr(3, 4, 5)));
        $this->assertEquals("(((1 OR 2) OR (3 OR 4)) OR (4 OR 5))", $op->bOr($op->bOr($op->bOr(1, 2), $op->bOr(3, 4)), $op->bOr(4, 5)));
    }

    public function testNot()
    {
        $op = new Op();
        $this->assertEquals("NOT `foo` = :foo", $op->bNot("foo", 2));
        $this->assertEquals(array(":foo" => 2), $op->getBinds());
        $op->clearBinds();

        $this->assertEquals("NOT `_foo` = :_foo", $op->bNot("_foo", "bar"));
        $this->assertEquals(array(":_foo" => "bar"), $op->getBinds());
        $op->clearBinds();

        $this->assertEquals("NOT `123foo` = :123foo", $op->bNot("123foo", "bar"));
        $this->assertEquals(array(":123foo" => "bar"), $op->getBinds());
        $op->clearBinds();
    }

    public function testEq()
    {
        $op = new Op();
        $this->assertEquals("`foo` = :foo", $op->eq("foo", 2));
        $this->assertEquals(array(":foo" => 2), $op->getBinds());
        $op->clearBinds();

        $this->assertEquals("`_foo` = :_foo", $op->eq("_foo", "bar"));
        $this->assertEquals(array(":_foo" => "bar"), $op->getBinds());
        $op->clearBinds();

        $this->assertEquals("`123foo` = :123foo", $op->eq("123foo", "bar"));
        $this->assertEquals(array(":123foo" => "bar"), $op->getBinds());
        $op->clearBinds();
    }

    public function testNeq()
    {
        $op = new Op();
        $this->assertEquals("`foo` != :foo", $op->neq("foo", 2));
        $this->assertEquals(array(":foo" => 2), $op->getBinds());
        $op->clearBinds();

        $this->assertEquals("`_foo` != :_foo", $op->neq("_foo", "bar"));
        $this->assertEquals(array(":_foo" => "bar"), $op->getBinds());
        $op->clearBinds();

        $this->assertEquals("`123foo` != :123foo", $op->neq("123foo", "bar"));
        $this->assertEquals(array(":123foo" => "bar"), $op->getBinds());
        $op->clearBinds();
    }

    public function testLt()
    {
        $op = new Op();
        $this->assertEquals("`foo` < :foo", $op->lt("foo", 2));
        $this->assertEquals(array(":foo" => 2), $op->getBinds());
        $op->clearBinds();

        $this->assertEquals("`_foo` < :_foo", $op->lt("_foo", "bar"));
        $this->assertEquals(array(":_foo" => "bar"), $op->getBinds());
        $op->clearBinds();

        $this->assertEquals("`123foo` < :123foo", $op->lt("123foo", "bar"));
        $this->assertEquals(array(":123foo" => "bar"), $op->getBinds());
        $op->clearBinds();
    }
}

?>