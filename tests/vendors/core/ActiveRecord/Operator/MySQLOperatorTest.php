<?php

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator\MySQL.php';

use Core\ActiveRecord\Operator\MySQL as Op;

class MySQLOperatorTest extends PHPUnit_Framework_TestCase
{
    public function testSetBinds()
    {
        Op::setBinds(array());
        $this->assertEquals(array(), Op::getBinds());
        Op::clearBinds();

        Op::setBinds(null);
        $this->assertEquals(array(), Op::getBinds());
        Op::clearBinds();

        Op::setBinds(array("foo" => "bar", "baz" => "blah", "hello" => "world"));
        $this->assertEquals(array(":foo" => "bar", ":baz" => "blah", ":hello" => "world"), Op::getBinds());
        Op::clearBinds();

        Op::setBinds(array("foo123" => "bar", "456baz" => "blah", "_hello" => "world", "test_" => 42));
        $this->assertEquals(array(":foo123" => "bar", ":456baz" => "blah", ":_hello" => "world", ":test_" => 42), Op::getBinds());
        Op::clearBinds();
    }

    /**
     * @expectedException Core\ActiveRecordOperatorException
     */
    public function testSetBindsException()
    {
        Op::setBinds(array("foo"));
    }

    public function testClearBinds()
    {
        Op::setBinds(array());
        Op::clearBinds();
        $this->assertEquals(array(), Op::getBinds());

        Op::setBinds(null);
        Op::clearBinds();
        $this->assertEquals(array(), Op::getBinds());

        Op::setBinds(array("foo" => "bar", "baz" => "blah", "hello" => "world"));
        Op::clearBinds();
        $this->assertEquals(array(), Op::getBinds());

        Op::setBinds(array("foo123" => "bar", "456baz" => "blah", "_hello" => "world", "test_" => 42));
        Op::clearBinds();
        $this->assertEquals(array(), Op::getBinds());
    }

    public function testAnd()
    {
        $this->assertEquals("(1 AND 2)", Op::bAnd(1, 2));
        $this->assertEquals("(1 AND 2 AND 3)", Op::bAnd(1, 2, 3));
        $this->assertEquals("(1 AND 2 AND 3 AND 4)", Op::bAnd(1, 2, 3, 4));
        $this->assertEquals("1", Op::bAnd(1));
        $this->assertEquals("", Op::bAnd());

        $this->assertEquals("((1 AND 2) AND (3 AND 4))", Op::bAnd(Op::bAnd(1, 2), Op::bAnd(3, 4)));
        $this->assertEquals("((1 AND 2 AND 3) AND (3 AND 4 AND 5))", Op::bAnd(Op::bAnd(1, 2, 3), Op::bAnd(3, 4, 5)));
        $this->assertEquals("(((1 AND 2) AND (3 AND 4)) AND (4 AND 5))", Op::bAnd(Op::bAnd(Op::bAnd(1, 2), Op::bAnd(3, 4)), Op::bAnd(4, 5)));
    }

    public function testOr()
    {
        $this->assertEquals("(1 OR 2)", Op::bOr(1, 2));
        $this->assertEquals("(1 OR 2 OR 3)", Op::bOr(1, 2, 3));
        $this->assertEquals("(1 OR 2 OR 3 OR 4)", Op::bOr(1, 2, 3, 4));
        $this->assertEquals("1", Op::bOr(1));
        $this->assertEquals("", Op::bOr());

        $this->assertEquals("((1 OR 2) OR (3 OR 4))", Op::bOr(Op::bOr(1, 2), Op::bOr(3, 4)));
        $this->assertEquals("((1 OR 2 OR 3) OR (3 OR 4 OR 5))", Op::bOr(Op::bOr(1, 2, 3), Op::bOr(3, 4, 5)));
        $this->assertEquals("(((1 OR 2) OR (3 OR 4)) OR (4 OR 5))", Op::bOr(Op::bOr(Op::bOr(1, 2), Op::bOr(3, 4)), Op::bOr(4, 5)));
    }

    public function testNot()
    {
        $this->assertEquals("NOT foo = :foo", Op::bNot("foo", 2));
        $this->assertEquals(array(":foo" => 2), Op::getBinds());
        Op::clearBinds();

        $this->assertEquals("NOT _foo = :_foo", Op::bNot("_foo", "bar"));
        $this->assertEquals(array(":_foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $this->assertEquals("NOT 123foo = :123foo", Op::bNot("123foo", "bar"));
        $this->assertEquals(array(":123foo" => "bar"), Op::getBinds());
        Op::clearBinds();
    }

    public function testEq()
    {
        $this->assertEquals("foo = :foo", Op::eq("foo", 2));
        $this->assertEquals(array(":foo" => 2), Op::getBinds());
        Op::clearBinds();

        $this->assertEquals("_foo = :_foo", Op::eq("_foo", "bar"));
        $this->assertEquals(array(":_foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $this->assertEquals("123foo = :123foo", Op::eq("123foo", "bar"));
        $this->assertEquals(array(":123foo" => "bar"), Op::getBinds());
        Op::clearBinds();
    }

    public function testNeq()
    {
        $this->assertEquals("foo != :foo", Op::neq("foo", 2));
        $this->assertEquals(array(":foo" => 2), Op::getBinds());
        Op::clearBinds();

        $this->assertEquals("_foo != :_foo", Op::neq("_foo", "bar"));
        $this->assertEquals(array(":_foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $this->assertEquals("123foo != :123foo", Op::neq("123foo", "bar"));
        $this->assertEquals(array(":123foo" => "bar"), Op::getBinds());
        Op::clearBinds();
    }

    public function testLt()
    {
        $this->assertEquals("foo < :foo", Op::lt("foo", 2));
        $this->assertEquals(array(":foo" => 2), Op::getBinds());
        Op::clearBinds();

        $this->assertEquals("_foo < :_foo", Op::lt("_foo", "bar"));
        $this->assertEquals(array(":_foo" => "bar"), Op::getBinds());
        Op::clearBinds();

        $this->assertEquals("123foo < :123foo", Op::lt("123foo", "bar"));
        $this->assertEquals(array(":123foo" => "bar"), Op::getBinds());
        Op::clearBinds();
    }
}

?>