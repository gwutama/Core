<?php

namespace Models;
use Core\ActiveRecord\AdapterServiceContainer;
use Core\ActiveRecord\Adapter\MySQL;
use Core\Utility\Spyc;
use Core\Storage\Config;
use Core\Storage\ConfigNode;

require_once 'vendors/Core/Utility/Spyc.php';
require_once 'vendors/Core/Utility/ObjectCollection.php';
require_once 'vendors/Core/Utility/Node.php';
require_once 'vendors/Core/Storage/Storageable.php';
require_once 'vendors/Core/Storage/Storage.php';
require_once 'vendors/Core/Storage/Config.php';
require_once 'vendors/Core/Storage/ConfigNode.php';
require_once 'vendors/Core/Service/ServiceContainer.php';
require_once 'vendors/Core/Service/Service.php';
require_once 'vendors/Core/ActiveRecord/Model.php';
require_once 'vendors/Core/ActiveRecord/ModelCollection.php';
require_once 'vendors/Core/ActiveRecord/Operatorable.php';
require_once 'vendors/Core/ActiveRecord/Operator/MySQL.php';
require_once 'vendors/Core/ActiveRecord/Adapter.php';
require_once 'vendors/Core/ActiveRecord/Adapter/MySQL.php';
require_once 'tests/resources/Models/SimpleMock.php';

class MySQLAdapterTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected function setUp()
    {
        $config = Spyc::YAMLLoad("tests/resources/database.yml");
        Config::setArray($config);
        Config::set("global.debug", true);

        $adapters = new AdapterServiceContainer();
        $this->object = $adapters->getService("default");

        $this->object->execute("DROP TABLE IF EXISTS simple_mocks");

        $this->object->execute(
            "CREATE TABLE simple_mocks(
                id INT PRIMARY KEY AUTO_INCREMENT,
                field1 VARCHAR(255),
                field2 VARCHAR(255),
                field3 INT
            )"
        );

        // Insert dummy records
        $this->object->create("SimpleMock", array(
            "field1" => "value1-1",
            "field2" => "value2-1",
            "field3" => 1
        ));

        $this->object->create("SimpleMock", array(
            "field1" => "value1-2",
            "field2" => "value2-2",
            "field3" => 1
        ));

        $this->object->create("SimpleMock", array(
            "field1" => "value1-3",
            "field2" => "value2-3",
            "field3" => 1
        ));
    }

    protected function tearDown()
    {
        $this->object->execute("DROP TABLE simple_mocks");
        $this->object->disconnect();
    }

    public function testFindById()
    {
        $obj = $this->object->findById("SimpleMock", 1);
        $this->assertEquals("value1-1", $obj->field1);
        $this->assertEquals("value2-1", $obj->field2);
        $this->assertEquals(1, $obj->field3);

        $obj = $this->object->findById("SimpleMock", 2);
        $this->assertEquals("value1-2", $obj->field1);
        $this->assertEquals("value2-2", $obj->field2);
        $this->assertEquals(1, $obj->field3);

        $obj = $this->object->findById("SimpleMock", 3);
        $this->assertEquals("value1-3", $obj->field1);
        $this->assertEquals("value2-3", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindByIdNotExists()
    {
        $obj = $this->object->findById("SimpleMock", 42);
        $this->assertNull($obj);
    }

    public function testFindAll()
    {
        $objs = $this->object->findAll("SimpleMock");
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

    public function testFindAllNotExists()
    {
        $this->object->execute("TRUNCATE TABLE simple_mocks");
        $objs = $this->object->findAll("SimpleMock");
        $this->assertEquals(0, $objs->count());
    }

    public function testFindFirst()
    {
        $obj = $this->object->findFirst("SimpleMock");
        $this->assertEquals("value1-1", $obj->field1);
        $this->assertEquals("value2-1", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindFirstNotExists()
    {
        $this->object->execute("TRUNCATE TABLE simple_mocks");
        $obj = $this->object->findFirst("SimpleMock");
        $this->assertNull($obj);
    }

    public function testFindLast()
    {
        $obj = $this->object->findLast("SimpleMock");
        $this->assertEquals("value1-3", $obj->field1);
        $this->assertEquals("value2-3", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindLastNotExists()
    {
        $this->object->execute("TRUNCATE TABLE simple_mocks");
        $obj = $this->object->findLast("SimpleMock");
        $this->assertNull($obj);
    }

    public function testFindOne()
    {
        $obj = $this->object->findOne("SimpleMock");
        $this->assertEquals("value1-1", $obj->field1);
        $this->assertEquals("value2-1", $obj->field2);
        $this->assertEquals(1, $obj->field3);
    }

    public function testFindOneNotExists()
    {
        $this->object->execute("TRUNCATE TABLE simple_mocks");
        $obj = $this->object->findOne("SimpleMock");
        $this->assertNull($obj);
    }

    public function testCreate()
    {
        // Insert dummy records
        for($i = 0; $i < 10; $i++) {
            $this->object->create("SimpleMock", array(
                "field1" => "value1-$i",
                "field2" => "value2-$i",
                "field3" => 1
            ));
        }

        $objects = $this->object->findAll("SimpleMock");
        $this->assertEquals(13, $objects->count()); // 10 + 3 from before
    }

    public function testUpdate()
    {
        $objects = $this->object->findAll("SimpleMock");
        $this->assertEquals(3, $objects->count());
        foreach($objects as $object) {
            $this->object->update($object, array(
                "field1" => "updated",
                "field2" => "test",
                "field3" => 42
            ));
        }

        $objects = $this->object->findAll("SimpleMock");
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
        $objects = $this->object->findAll("SimpleMock");
        $this->assertEquals(3, $objects->count());
        $this->object->updateAll($objects, array(
            "field1" => "updated",
            "field2" => "test",
            "field3" => 42
        ));

        $objects = $this->object->findAll("SimpleMock");
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
        $objects = $this->object->findAll("SimpleMock");
        $this->assertEquals(3, $objects->count());
        foreach($objects as $object) {
            $this->object->delete($object);
        }
        $objects = $this->object->findAll("SimpleMock");
        $this->assertEquals(0, $objects->count());
    }

    public function testDeleteAll()
    {
        $objects = $this->object->findAll("SimpleMock");
        $this->assertEquals(3, $objects->count());

        $objects->delete();

        $objects = $this->object->findAll("SimpleMock");
        $this->assertEquals(0, $objects->count());
    }
}

?>