<?php

namespace Core;

class Mock {
    public $param1;
    public $param2;
    public $param3;
}

class Mock2 extends Mock {
    public function __construct($param1, $param2, $param3) {
        $this->param1 = $param1;
        $this->param2 = $param2;
        $this->param3 = $param3;
    }
}

class Mock3 extends Mock2 {}


require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\Service.php';

/**
 * Test class for Service.
 * Generated by PHPUnit on 2012-02-17 at 05:14:21.
 */
class ServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstance()
    {
        $service = new Service("\\Core\\Mock");
        $obj = $service->getInstance();
        $this->assertTrue($obj instanceof Mock);

        $service = new Service("\\Core\\Mock2", array(
            "param1" => "value1",
            "param2" => "value2",
            "param3" => "value3"
        ));
        $obj = $service->getInstance();
        $this->assertTrue($obj instanceof Mock2);
        $this->assertEquals("value1", $obj->param1);
        $this->assertEquals("value2", $obj->param2);
        $this->assertEquals("value3", $obj->param3);

        $service = new Service("\\Core\\Mock3", array(
            "param1" => "value1",
            "param2" => "value2",
            "param3" => "value3"
        ));
        $obj = $service->getInstance();
        $this->assertTrue($obj instanceof Mock3);
        $this->assertEquals("value1", $obj->param1);
        $this->assertEquals("value2", $obj->param2);
        $this->assertEquals("value3", $obj->param3);
    }


    /**
     * @expectedException \Core\CannotCreateServiceException
     */
    public function testGetInstance2()
    {
        $service = new Service("Inexists");
        $service->getInstance();
    }
}
?>
