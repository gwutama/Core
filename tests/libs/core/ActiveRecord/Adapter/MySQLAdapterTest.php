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
        $this->assertEquals("INSERT INTO models(foo) VALUES(?)", $q);

        $q = MySQL::insertQuery("Person", array("foo" => "bar", "baz" => "blah"));
        $this->assertEquals("INSERT INTO people(foo, baz) VALUES(?, ?)", $q);

        $q = MySQL::insertQuery("Student", array("foo" => "bar", "baz" => "blah"), array(
            "on duplicate key update" => array("hello" => "world")
        ));
        //$this->assertEquals("INSERT INTO people(foo, baz) VALUES(?, ?) ON DUPLICATE KEY UPDATE hello = 'world'", $q);
    }
}

?>