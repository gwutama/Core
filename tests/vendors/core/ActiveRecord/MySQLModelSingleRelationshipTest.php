<?php
namespace Core\ActiveRecord;
use Core\ActiveRecord\Adapter\MySQL;
use Core\Utility\Spyc;
use Core\Storage\Config;
use Core\Storage\ConfigNode;
use Models\Mock;
use Models\Single;

require_once 'vendors/Core/Utility/Spyc.php';
require_once 'vendors/Core/Utility/ObjectCollection.php';
require_once 'vendors/Core/Storage/Storageable.php';
require_once 'vendors/Core/Storage/Storage.php';
require_once 'vendors/Core/Storage/StorageNode.php';
require_once 'vendors/Core/Storage/Config.php';
require_once 'vendors/Core/Storage/ConfigNode.php';
require_once 'vendors/Core/Service/ServiceContainer.php';
require_once 'vendors/Core/Service/Service.php';
require_once 'vendors/Core/ActiveRecord/AdapterServiceContainer.php';
require_once 'vendors/Core/ActiveRecord/Model.php';
require_once 'vendors/Core/ActiveRecord/ModelCollection.php';
require_once 'vendors/Core/ActiveRecord/Operatorable.php';
require_once 'vendors/Core/ActiveRecord/Operator/MySQL.php';
require_once 'tests/resources/Models/Mock.php';
require_once 'tests/resources/Models/Single.php';

class MySQLModelSingleRelationshipTest extends \PHPUnit_Framework_TestCase
{
    protected $mock;
    protected $single;


    protected function setUp()
    {
        $config = Spyc::YAMLLoad("tests/resources/database.yml");
        Config::setArray($config);
        Config::set("global.debug", true);

        $adapters = new AdapterServiceContainer();
        $adapter = $adapters->getService("default");

        $adapter->execute("DROP TABLE IF EXISTS singles");
        $adapter->execute("DROP TABLE IF EXISTS mocks");

        $adapter->execute(
            "CREATE TABLE mocks(
                id INT PRIMARY KEY AUTO_INCREMENT,
                field1 VARCHAR(255),
                field2 VARCHAR(255),
                field3 INT
            )"
        );
        $adapter->execute(
            "CREATE TABLE singles(
                id INT PRIMARY KEY AUTO_INCREMENT,
                field VARCHAR(255),
                mocks_id INT,
                FOREIGN KEY(mocks_id) REFERENCES mocks(id)
            )"
        );

        $this->mock = new Mock();
        $this->single = new Single();

        // Insert dummy records
        $mock = new Mock();
        $mock->field1 = "value1-1";
        $mock->field2 = "value2-1";
        $mock->field3 = 1;
        $mock->save();

        $single = new Single();
        $single->field = "test single";
        $single->mocksId = $mock->id;
        $single->save();

        $mock = new Mock();
        $mock->field1 = "value1-2";
        $mock->field2 = "value2-2";
        $mock->field3 = 2;
        $mock->save();

        $single = new Single();
        $single->field = "test single 2";
        $single->mocksId = $mock->id;
        $single->save();
    }

    protected function tearDown()
    {
        $this->single->execute("DROP TABLE singles");
        $this->mock->execute("DROP TABLE mocks");
    }

    public function testFindById()
    {
        $mock = Mock::findById(1);
        $this->assertEquals("value1-1", $mock->field1);
        $this->assertEquals("value2-1", $mock->field2);
        $this->assertEquals(1, $mock->field3);

        $single = Single::findById(1);
        $this->assertEquals("test single", $single->field);
        $this->assertEquals(1, $single->mocksId);

        $mock = Mock::findById(1);
        $this->assertEquals(1, $mock->id);
        $this->assertEquals("value1-1", $mock->field1);
        $this->assertEquals("value2-1", $mock->field2);
        $this->assertEquals(1, $mock->field3);
        $this->assertEquals("test single", $mock->singleField);
        $this->assertEquals(1, $mock->singleId);
        $this->assertEquals(1, $mock->singleMocksId);
    }

    public function testFindById2()
    {
        $mock = Mock::findById(2);
        $this->assertEquals("value1-2", $mock->field1);
        $this->assertEquals("value2-2", $mock->field2);
        $this->assertEquals(2, $mock->field3);

        $single = Single::findById(2);
        $this->assertEquals("test single 2", $single->field);
        $this->assertEquals(2, $single->mocksId);

        $mock = Mock::findById(2);
        $this->assertEquals(2, $mock->id);
        $this->assertEquals("value1-2", $mock->field1);
        $this->assertEquals("value2-2", $mock->field2);
        $this->assertEquals(2, $mock->field3);
        $this->assertEquals("test single 2", $mock->singleField);
        $this->assertEquals(2, $mock->singleId);
        $this->assertEquals(2, $mock->singleMocksId);
    }

    public function testFindById3()
    {
        $mock = Mock::findById(1, array(
            "fields" => array("id", "field1", "field2", "field3")
        ));
        $this->assertEquals(1, $mock->id);
        $this->assertEquals("value1-1", $mock->field1);
        $this->assertEquals("value2-1", $mock->field2);
        $this->assertEquals(1, $mock->field3);
        $this->assertNull($mock->singleField);
        $this->assertNull($mock->singleId);
        $this->assertNull($mock->singleMocksId);
    }

    public function testFindById4()
    {
        $mock = Mock::findById(1, array(
            "fields" => array("id", "field2")
        ));
        $this->assertEquals(1, $mock->id);
        $this->assertNull($mock->field1);
        $this->assertEquals("value2-1", $mock->field2);
        $this->assertNull($mock->field3);
        $this->assertNull($mock->singleField);
        $this->assertNull($mock->singleId);
        $this->assertNull($mock->singleMocksId);
    }

    public function testFindById5()
    {
        $mock = Mock::findById(2, array(
            "fields" => array("field2")
        ));
        $this->assertNull($mock->id);
        $this->assertNull($mock->field1);
        $this->assertEquals("value2-2", $mock->field2);
        $this->assertNull($mock->field3);
        $this->assertNull($mock->singleField);
        $this->assertNull($mock->singleId);
        $this->assertNull($mock->singleMocksId);
    }

    public function testFindById6()
    {
        $mock = Mock::findById(2, array(
            "fields" => array("singles.field", "singles.id", "singles.mocks_id")
        ));
        $this->assertNull($mock->id);
        $this->assertNull($mock->field1);
        $this->assertNull($mock->field2);
        $this->assertNull($mock->field3);
        $this->assertEquals("test single 2", $mock->singleField);
        $this->assertEquals(2, $mock->singleId);
        $this->assertEquals(2, $mock->singleMocksId);
    }


}

?>