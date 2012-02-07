<?php

require_once 'C:\Users\Galuh Utama\workspace\Core\libs\Core\ActiveRecord\Operator.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\libs\Core\ActiveRecord\Operator\MySQL.php';

use Core\ActiveRecord\Operator\MySQL as Op;

class MySQLOperatorTest extends PHPUnit_Framework_TestCase
{
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
}

?>