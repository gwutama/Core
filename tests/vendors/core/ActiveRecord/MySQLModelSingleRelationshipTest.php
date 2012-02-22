<?php
namespace Core\ActiveRecord;
use Core\ActiveRecord\Adapter\MySQL;
use Core\Spyc;
use Core\Storage\Config;
use Core\Storage\ConfigNode;
use Models\Mock;
use Models\Single;

require_once 'vendors/Core/Spyc.php';
require_once 'vendors/Core/Storage/Storageable.php';
require_once 'vendors/Core/Storage/BaseStorage.php';
require_once 'vendors/Core/Storage/BaseStorageNode.php';
require_once 'vendors/Core/Storage/Config.php';
require_once 'vendors/Core/Storage/ConfigNode.php';
require_once 'vendors/Core/ServiceContainer.php';
require_once 'vendors/Core/Service.php';
require_once 'vendors/Core/ActiveRecord/AdapterServiceContainer.php';
require_once 'vendors/Core/ActiveRecord/Model.php';
require_once 'vendors/Core/ActiveRecord/ModelCollection.php';
require_once 'vendors/Core/ActiveRecord/Operator.php';
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

        $adapter->setModel("Mock");

        $adapter->execute("DROP TABLE IF EXISTS mocks");
        $adapter->execute("DROP TABLE IF EXISTS singles");

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
    }

    protected function tearDown()
    {
        $this->mock->execute("DROP TABLE mocks");
        $this->single->execute("DROP TABLE singles");
    }

    public function testFindById()
    {
        $mock = $this->mock->findById(1);
        $this->assertEquals("value1-1", $mock->field1);
        $this->assertEquals("value2-1", $mock->field2);
        $this->assertEquals(1, $mock->field3);

        $single = $this->single->findById(1);
        $this->assertEquals("test single", $single->field);
        $this->assertEquals(1, $single->mocksId);

        /*
        $single = $mock->single;
        $this->assertTrue($single instanceof Single);
        $this->assertEquals("test single", $single->field);
        $this->assertEquals(1, $single->mocksId);
        */
    }
}

?>