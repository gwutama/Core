<?php

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\Config.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\ConfigNode.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\exceptions.php';

use Core\Config;
use Core\ConfigNode;
use Core\InvalidConfigKeyException;


/**
 * Test class for Config.
 * Generated by PHPUnit on 2012-01-28 at 00:17:40.
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Config
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = new Config;
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testSet()
    {
        Config::set("hello.world.long.key1.key2", "test");
        Config::set("foo", "BAR");
        Config::set("foo.bar", "BAZ");
        Config::set("foo.bar.baz", "BLAH");
        Config::set("baz", "BLAH");
        Config::set("acme.foobar", array("foo", "bar"));
        Config::set("long.long2.long3", "hello");
        Config::set("long.long2", "hello2");
        Config::set("long", "hello3");
        Config::set("null.value", null);
        Config::set("null", null);
    }

    public function testGet()
    {
        $this->assertEquals(Config::get("hello.world.long.key1.key2"), "test");
        $this->assertEquals(Config::get("foo"), "BAR");
        $this->assertEquals(Config::get("foo.bar"), "BAZ");
        $this->assertEquals(Config::get("foo.bar.baz"), "BLAH");
        $this->assertEquals(Config::get("baz"), "BLAH");
        $this->assertEquals(Config::get("acme.foobar"), array("foo", "bar"));
        $this->assertEquals(Config::get("long.long2.long3"), "hello");
        $this->assertEquals(Config::get("long.long2"), "hello2");
        $this->assertEquals(Config::get("long"), "hello3");
        $this->assertEquals(Config::get("null.value"), null);
        $this->assertEquals(Config::get("null"), null);
        $this->assertNull(Config::get("lorem.ipsum.dolor"));
        $this->assertNull(Config::get("olo.lol"));
        $this->assertNull(Config::get("123"));
    }

    /**
     * @expectedException Core\InvalidConfigKeyException
     */
    public function testValidKey()
    {
        Config::set("(", "foo");
    }

    /**
     * @expectedException Core\InvalidConfigKeyException
     */
    public function testValidKey2()
    {
        Config::set("hel\$lo.-ddd-", "foo");
    }

    public function testSetArray()
    {
        Config::clear();
        $config = array(
            "foo" => array(
                "bar" => "baz"
            ),
            "hello" => "world"
        );
        Config::setArray($config);
        $this->assertEquals("baz", Config::get("foo.bar"));
        $this->assertEquals("world", Config::get("hello"));
        $this->assertNull(Config::get("acme"));
        $this->assertNull(Config::get("acme.foobar"));
        Config::clear();

        $config = array(
            "hello" => "world",
            "foo" => array(
                "bar" => "baz"
            )
        );
        Config::setArray($config);
        $this->assertEquals("baz", Config::get("foo.bar"));
        $this->assertEquals("world", Config::get("hello"));
        $this->assertNull(Config::get("acme"));
        $this->assertNull(Config::get("acme.foobar"));
        Config::clear();

        $config = array(
            "hello" => "world",
            "foo" => array(
                "test" => 123,
                "bar" => "baz",
                "array" => array(
                    123 => 456,
                    "key" => "value",
                    "key2" => "value2",
                    "array2" => array(
                        "key" => "value",
                        "key2" => "value2",
                        "key3" => "value3"
                    )
                )
            ),
            "fizz" => "buzz",
            "foo2" => array(
                "test" => 123,
                "bar" => "baz",
                "array" => array(
                    123 => 456,
                    "key" => "value",
                    "key2" => "value2",
                    "array2" => array(
                        "key" => "value",
                        "key2" => "value2",
                        "key3" => "value3"
                    )
                )
            ),
        );
        Config::setArray($config);

        $this->assertNull(Config::get("test"));
        $this->assertEquals("world", Config::get("hello"));

        $this->assertEquals("baz", Config::get("foo.bar"));
        $this->assertEquals(123, Config::get("foo.test"));
        $this->assertEquals(456, Config::get("foo.array.123"));
        $this->assertEquals("value", Config::get("foo.array.key"));
        $this->assertEquals("value2", Config::get("foo.array.key2"));
        $this->assertEquals("value", Config::get("foo.array.array2.key"));
        $this->assertEquals("value2", Config::get("foo.array.array2.key2"));
        $this->assertEquals("value3", Config::get("foo.array.array2.key3"));

        $this->assertEquals("baz", Config::get("foo2.bar"));
        $this->assertEquals(123, Config::get("foo2.test"));
        $this->assertEquals(456, Config::get("foo2.array.123"));
        $this->assertEquals("value", Config::get("foo2.array.key"));
        $this->assertEquals("value2", Config::get("foo2.array.key2"));
        $this->assertEquals("value", Config::get("foo2.array.array2.key"));
        $this->assertEquals("value2", Config::get("foo2.array.array2.key2"));
        $this->assertEquals("value3", Config::get("foo2.array.array2.key3"));

        $this->assertEquals("buzz", Config::get("fizz"));
        $this->assertNull(Config::get("acme"));
        $this->assertNull(Config::get("acme.foobar"));

        Config::clear();
    }

    public function testSetArray2()
    {
        $config = array("foo" => array(1, "bar", "baz"));
        Config::setArray($config, true);
        $this->assertEquals(array(1, "bar", "baz"), Config::get("foo"));
        Config::clear();

        $config = array("foo" => array(1, "bar", "baz"), "hello" => "world", "test" => array("foo", "bar", "baz"));
        Config::setArray($config, true);
        $this->assertEquals(array(1, "bar", "baz"), Config::get("foo"));
        $this->assertEquals("world", Config::get("hello"));
        $this->assertEquals(array("foo", "bar", "baz"), Config::get("test"));
        Config::clear();
    }

    /**
     * @expectedException Core\InvalidConfigKeyException
     */
    public function testValidKey3()
    {
        $config = array(
            "foo" => "bar",
            "blah"
        );
        Config::setArray($config);
        Config::clear();
    }

    /**
     * @expectedException Core\InvalidConfigKeyException
     */
    public function testValidKey4()
    {
        $config = array("d$\\dd");
        Config::setArray($config);
        Config::clear();
    }

    /**
     * @expectedException Core\InvalidConfigKeyException
     */
    public function testValidKey5()
    {
        $config = "--";
        Config::setArray($config);
        Config::clear();
    }

    /**
     * @expectedException Core\InvalidConfigKeyException
     */
    public function testValidKey6()
    {
        $config = "foobar";
        Config::setArray($config);
        Config::clear();
    }
}
?>