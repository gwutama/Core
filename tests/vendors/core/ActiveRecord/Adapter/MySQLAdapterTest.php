<?php

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Model.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\ModelCollection.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Adapter.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Adapter\MySQL.php';

class Mock extends \Core\ActiveRecord\Model {}

use Core\ActiveRecord\Adapter\MySQL;

class MySQLAdapterTest extends PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $this->object = new MySQL("mysql:host=localhost;dbname=test", "root", "gwutama");
        $this->object->setModel("Mock");
        $this->object->execute(
            "CREATE TABLE mocks(
                id INT PRIMARY KEY AUTO_INCREMENT,
                field1 VARCHAR(255),
                field2 VARCHAR(255),
                field3 INT
            )"
        );

        // Insert dummy records
        $this->object->create(array(
            "field1" => "value1-1",
            "field2" => "value2-1",
            "field3" => 1
        ));

        $this->object->create(array(
            "field1" => "value1-2",
            "field2" => "value2-2",
            "field3" => 1
        ));

        $this->object->create(array(
            "field1" => "value1-3",
            "field2" => "value2-3",
            "field3" => 1
        ));
    }

    protected function tearDown()
    {
        $this->object->execute("DROP TABLE mocks");
        $this->object->disconnect();
    }

    public function testFindById()
    {
        $obj = $this->object->findById("id", 1);
        $this->assertEquals("value1-1", $obj->field1);
        $this->assertEquals("value2-1", $obj->field2);
        $this->assertEquals(1, $obj->field3);

        $obj = $this->object->findById("id", 2);
        $this->assertEquals("value1-2", $obj->field1);
        $this->assertEquals("value2-2", $obj->field2);
        $this->assertEquals(1, $obj->field3);

        $obj = $this->object->findById("id", 3);
        $this->assertEquals("value1-3", $obj->field1);
        $this->assertEquals("value2-3", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindAll()
    {
        $objs = $this->object->findAll("id");
        $this->assertEquals(3, $objs->count());

        $this->assertEquals("value1-1", $objs->get(0)->field1);
        $this->assertEquals("value2-1", $objs->get(0)->field2);
        $this->assertEquals(1, $objs->get(0)->field3);

        $this->assertEquals("value1-2", $objs->get(1)->field1);
        $this->assertEquals("value2-2", $objs->get(1)->field2);
        $this->assertEquals(1, $objs->get(1)->field3);

        $this->assertEquals("value1-3", $objs->get(2)->field1);
        $this->assertEquals("value2-3", $objs->get(2)->field2);
        $this->assertEquals(1, $objs->get(2)->field3);
    }

    public function testFindFirst()
    {
        $obj = $this->object->findFirst("id");
        $this->assertEquals("value1-1", $obj->field1);
        $this->assertEquals("value2-1", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindLast()
    {
        $obj = $this->object->findLast("id");
        $this->assertEquals("value1-3", $obj->field1);
        $this->assertEquals("value2-3", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindOne()
    {
        $obj = $this->object->findOne();
        $this->assertEquals("value1-1", $obj->field1);
        $this->assertEquals("value2-1", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testCreate()
    {
        // Insert dummy records
        for($i = 0; $i < 10; $i++) {
            $this->object->create(array(
                "field1" => "value1-$i",
                "field2" => "value2-$i",
                "field3" => 1
            ));
        }

        $objects = $this->object->findAll("id");
        $this->assertEquals(13, $objects->count()); // 10 + 3 from before
    }

    public function testUpdate()
    {
        $objects = $this->object->findAll("id");
        $this->assertEquals(3, $objects->count());
        foreach($objects as $object) {
            $this->object->update($object, array(
                "field1" => "updated",
                "field2" => "test",
                "field3" => 42
            ));
        }

        $objects = $this->object->findAll("id");
        $this->assertEquals(3, $objects->count());
        $this->assertEquals("updated", $objects->get(0)->field1);
        $this->assertEquals("test", $objects->get(0)->field2);
        $this->assertEquals(42, $objects->get(0)->field3);

        $this->assertEquals("updated", $objects->get(1)->field1);
        $this->assertEquals("test", $objects->get(1)->field2);
        $this->assertEquals(42, $objects->get(1)->field3);

        $this->assertEquals("updated", $objects->get(2)->field1);
        $this->assertEquals("test", $objects->get(2)->field2);
        $this->assertEquals(42, $objects->get(2)->field3);
    }

    public function testUpdateAll()
    {
        $objects = $this->object->findAll("id");
        $this->assertEquals(3, $objects->count());
        $this->object->updateAll($objects, array(
            "field1" => "updated",
            "field2" => "test",
            "field3" => 42
        ));

        $objects = $this->object->findAll("id");
        $this->assertEquals(3, $objects->count());
        $this->assertEquals("updated", $objects->get(0)->field1);
        $this->assertEquals("test", $objects->get(0)->field2);
        $this->assertEquals(42, $objects->get(0)->field3);

        $this->assertEquals("updated", $objects->get(1)->field1);
        $this->assertEquals("test", $objects->get(1)->field2);
        $this->assertEquals(42, $objects->get(1)->field3);

        $this->assertEquals("updated", $objects->get(2)->field1);
        $this->assertEquals("test", $objects->get(2)->field2);
        $this->assertEquals(42, $objects->get(2)->field3);
    }

    public function testDelete()
    {
        $objects = $this->object->findAll("id");
        $this->assertEquals(3, $objects->count());
        foreach($objects as $object) {
            $object->delete();
        }
        $objects = $this->object->findAll("id");
        $this->assertEquals(0, $objects->count());
    }

    public function testDeleteAll()
    {
        $objects = $this->object->findAll("id");
        $this->assertEquals(3, $objects->count());

        $objects->delete();

        $objects = $this->object->findAll("id");
        $this->assertEquals(0, $objects->count());
    }
}

?>