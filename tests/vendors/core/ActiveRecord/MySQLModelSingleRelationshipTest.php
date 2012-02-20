<?php
/*
namespace Core\ActiveRecord;
use Core\ActiveRecord\Adapter\MySQL;
use Models\Mock;
use Models\Single;

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Model.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\ModelCollection.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ActiveRecord\Operator\MySQL.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\tests\resources\Models\Mock.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\tests\resources\Models\Single.php';

class MySQLModelSingleRelationshipTest extends \PHPUnit_Framework_TestCase
{
    protected $object;

    protected $adapter;


    protected function setUp()
    {
        $this->adapter = new MySQL("mysql:host=localhost;dbname=test", "root", "gwutama");
        $this->object = new MySQL("mysql:host=localhost;dbname=test", "root", "gwutama");
        $this->object->setModel("Mock");
        $this->object->execute(
            "CREATE TABLE mocks(
                id INT PRIMARY KEY AUTO_INCREMENT,
                field VARCHAR(255),
            )"
        );
        $this->object->execute(
            "CREATE TABLE singles(
                id INT PRIMARY KEY AUTO_INCREMENT,
                field VARCHAR(255),
                mocks_id INT,
                FOREIGN KEY(mocks_id) REFERENCES mocks(id)
            )"
        );

        // Insert dummy records
        $test = new Mock($this->adapter);
        $test->field = "test";
        $test->save();

        $single = new Single($this->adapter);
        $single->field = "test";
        $single->mocksId = $test->id;
        $single->save();
    }

    protected function tearDown()
    {
        $this->object->execute("DROP TABLE tests");
        $this->object->execute("DROP TABLE singles");
        $this->object->disconnect();
    }

    public function testFindById()
    {
    }
}

*/

?>