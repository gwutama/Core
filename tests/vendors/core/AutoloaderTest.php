<?php

require_once 'vendors/Core/Autoloader.php';
require_once 'vendors/Core/Storage/Storageable.php';
require_once 'vendors/Core/Storage/BaseStorage.php';
require_once 'vendors/Core/Storage/BaseStorageNode.php';
require_once 'vendors/Core/Storage/Config.php';
require_once 'vendors/Core/Storage/ConfigNode.php';

define("DS", DIRECTORY_SEPARATOR);

use Core\Autoloader;
use Core\Storage\Config;
use Core\Template;

/**
 * Test class for Autoloader.
 * Generated by PHPUnit on 2012-02-13 at 01:28:53.
 */
class AutoloaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Autoloader
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Autoloader;
        $this->object->register("app/");
        $this->object->register("vendors/");
        $this->object->register("libs/");
        $this->object->register("models/");
        $this->object->register("foobar/");
        $this->object->register("acme/");
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        $this->object->unregister();
    }

    public function testRegister()
    {
        $this->assertEquals(array("app/", "vendors/", "libs/", "models/", "foobar/", "acme/"),
            $this->object->getDirs());
    }

    public function testLoader()
    {
        $foo = new Config;
        //$foo = new \Controllers\Home("Foo", "bar");
        $foo = new Template("foo", "bar", "baz");
    }

    /**
     * @expectedException Core\FileNotFoundException
     */
    public function testLoaderException()
    {
        $foo = new NotAvailable;
    }
}
?>
