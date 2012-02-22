<?php

namespace Models;
use Core\ActiveRecord\AdapterServiceContainer;
use Core\ActiveRecord\Adapter\MySQL;
use Core\Spyc;
use Core\Storage\Config;
use Core\Storage\ConfigNode;

require_once 'vendors/Core/Spyc.php';
require_once 'vendors/Core/Storage/Storageable.php';
require_once 'vendors/Core/Storage/BaseStorage.php';
require_once 'vendors/Core/Storage/BaseStorageNode.php';
require_once 'vendors/Core/Storage/Config.php';
require_once 'vendors/Core/Storage/ConfigNode.php';
require_once 'vendors/Core/ServiceContainer.php';
require_once 'vendors/Core/Service.php';
require_once 'vendors/Core/ActiveRecord\AdapterServiceContainer.php';
require_once 'vendors/Core/ActiveRecord\Model.php';
require_once 'vendors/Core/ActiveRecord\ModelCollection.php';
require_once 'vendors/Core/ActiveRecord\Operator.php';
require_once 'vendors/Core/ActiveRecord\Operator\MySQL.php';
require_once 'tests/resources/Models/Mock.php';

/**
 * Test class for Model.
 * Generated by PHPUnit on 2012-02-19 at 00:41:17.
 */
class MySQLModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $config = Spyc::YAMLLoad("tests/resources/database.yml");
        Config::setArray($config);
        Config::set("global.debug", true);

        $adapters = new AdapterServiceContainer();
        $adapter = $adapters->getService("default");

        $adapter->setModel("Mock");

        $adapter->execute("DROP TABLE IF EXISTS mocks");

        $adapter->execute(
            "CREATE TABLE mocks(
                id INT PRIMARY KEY AUTO_INCREMENT,
                field1 VARCHAR(255),
                field2 VARCHAR(255),
                field3 INT
            )"
        );

        $this->object = new Mock();

        // Insert dummy records
        // ID 1
        $mock = new Mock();
        $mock->field1 = "value1-1";
        $mock->field2 = "value2-1";
        $mock->field3 = 1;
        $mock->save();

        // ID 2
        $mock = new Mock();
        $mock->field1 = "value1-2";
        $mock->field2 = "value2-2";
        $mock->field3 = 1;
        $mock->save();

        // ID 3
        $mock = new Mock();
        $mock->field1 = "value1-3";
        $mock->field2 = "value2-3";
        $mock->field3 = 1;
        $mock->save();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object->execute("DROP TABLE mocks");
    }

    public function testFindById()
    {
        $obj = $this->object->findById(1);
        $this->assertEquals("value1-1", $obj->field1);
        $this->assertEquals("value2-1", $obj->field2);
        $this->assertEquals(1, $obj->field3);

        $obj = $this->object->findById(2);
        $this->assertEquals("value1-2", $obj->field1);
        $this->assertEquals("value2-2", $obj->field2);
        $this->assertEquals(1, $obj->field3);

        $obj = $this->object->findById(3);
        $this->assertEquals("value1-3", $obj->field1);
        $this->assertEquals("value2-3", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindAll()
    {
        $objs = $this->object->findAll();
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
        $obj = $this->object->findFirst();
        $this->assertEquals("value1-1", $obj->field1);
        $this->assertEquals("value2-1", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindLast()
    {
        $obj = $this->object->findLast();
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
            $mock = new Mock();
            $mock->save();
        }

        $objects = $this->object->findAll();
        $this->assertEquals(13, $objects->count()); // 10 + 3 from before
    }

    public function testUpdate()
    {
        $objects = $this->object->findAll();
        $this->assertEquals(3, $objects->count());
        foreach($objects as $object) {
            $object->field1 = "updated";
            $object->field2 = "test";
            $object->field3 = 42;
            $object->save();
        }

        $objects = $this->object->findAll();
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
        $objects = $this->object->findAll();
        $this->assertEquals(3, $objects->count());
        $objects->save(array(
            "field1" => "updated",
            "field2" => "test",
            "field3" => 42
        ));

        $objects = $this->object->findAll();
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
        $objects = $this->object->findAll();
        $this->assertEquals(3, $objects->count());
        foreach($objects as $object) {
            $object->delete();
        }
        $objects = $this->object->findAll();
        $this->assertEquals(0, $objects->count());
    }


    public function testDeleteAll()
    {
        $objects = $this->object->findAll();
        $this->assertEquals(3, $objects->count());

        $objects->delete();

        $objects = $this->object->findAll();
        $this->assertEquals(0, $objects->count());
    }

}
?>
