<?php

require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\RouteParser.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\RoutingObject.php';
require_once 'C:\Users\Galuh Utama\workspace\Core\vendors\Core\exceptions.php';

use Core\RouteParser;
use Core\RoutingObject;

/**
 * Test class for RouteParser.
 * Generated by PHPUnit on 2012-01-28 at 14:32:16.
 */
class RouteParserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var RouteParser
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $routes = array(
            array(
                "pattern" => "/foo/{bar}/{baz}/{blah}",
                "controller" => "Foobar",
                "action" => "index"
            ),
            array(
                "pattern" => "/greetings/{hello}/{name}/{country}.html",
                "controller" => "Greetings",
                "action" => "show"
            ),
            array(
                "pattern" => "/category/{category}/read/{id}/{slug}.html",
                "controller" => "Blog",
                "action" => "read"
            ),
        );

        $this->object = new RouteParser($routes);
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    public function testParse()
    {
        $url = "/";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Home", "index"));

        $url = "/controller";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Home", "index"));

        $url = "/controller/";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Controller", "index"));

        $url = "/controller/action/";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Controller", "action"));

        $url = "/controller/action";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Controller", "index"));

        $url = "/controller/action/param1:value1/";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Controller", "action",
            array("param1" => "value1")));

        $url = "/controller/action/param1:value1/param2:value2/";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Controller", "action",
            array("param1" => "value1", "param2" => "value2")));

        $url = "/controller/action/param1:value1";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Controller", "action",
            array("param1" => "value1")));

        // Parameter is omitted because it is invalid
        $url = "/controller/action/param1/";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Controller", "action",
            array()));

        // Parameters are omitted because they are invalid
        $url = "/controller/action/param1/param2/param3/param4";
        $routing = $this->object->parse($url);
        $this->assertEquals($routing, new RoutingObject($url, "Controller", "action",
            array()));
    }

    /**
     * @expectedException Core\InvalidRouteException
     */
    public function testParse2()
    {
        $this->object->parse("");
    }

    /**
     * @expectedException Core\InvalidRouteException
     */
    public function testParse3()
    {
        $this->object->parse("//");
    }

    /**
     * @expectedException Core\InvalidRouteException
     */
    public function testParse4()
    {
        $this->object->parse("////");
    }

    /**
     * @expectedException Core\InvalidRouteException
     */
    public function testParse5()
    {
        $this->object->parse("?df2=foo");
    }

    public function testGetParamBetween()
    {
        $tmp = RouteParser::getParamBetween("test", 0, "", "");
        $this->assertEquals("test", $tmp);

        $tmp = RouteParser::getParamBetween("test", 0, "", "t");
        $this->assertEquals("", $tmp);

        $tmp = RouteParser::getParamBetween("foobar", 0, "", "b");
        $this->assertEquals("foo", $tmp);

        $tmp = RouteParser::getParamBetween("test", 0, "t", "");
        $this->assertEquals("est", $tmp);

        $tmp = RouteParser::getParamBetween("test", 0, "t", "t");
        $this->assertEquals("es", $tmp);

        $tmp = RouteParser::getParamBetween("/foo/1/2/3", 0, "/", "/");
        $this->assertEquals("foo", $tmp);

        $tmp = RouteParser::getParamBetween("/foo/1/2/3", 4, "/", "/");
        $this->assertEquals("1", $tmp);

        $tmp = RouteParser::getParamBetween("/foo/1/2/3", 6, "/", "/");
        $this->assertEquals("2", $tmp);

        $tmp = RouteParser::getParamBetween("/foo/1/2/3", 8, "/", "");
        $this->assertEquals("3", $tmp);
    }

    public function testParseParamsFromRoute()
    {
        $params = $this->object->parseParamsFromRoute("/foo/{bar}/{baz}/{blah}", "/foo/1/2/3");
        $this->assertEquals($params, array("bar" => 1, "baz" => 2, "blah" => 3));

        $params = $this->object->parseParamsFromRoute("/foo/{hello}/{longlonglong}/{test}", "/foo/1/2/3");
        $this->assertEquals($params, array("hello" => 1, "longlonglong" => 2, "test" => 3));

        $params = $this->object->parseParamsFromRoute("/{hello}/{longlonglong}/{test}", "/1/2/3");
        $this->assertEquals($params, array("hello" => 1, "longlonglong" => 2, "test" => 3));

        $params = $this->object->parseParamsFromRoute("/{hello}/{longlonglong}/{test}.html", "/lorem/ipsum/hello.html");
        $this->assertEquals($params, array("hello" => "lorem", "longlonglong" => "ipsum", "test" => "hello"));

        $params = $this->object->parseParamsFromRoute("/foo/bar/baz/{param1}-{param2}-{param3}/{param4}/{param5}/",
            "/foo/bar/baz/value1-value2-value3/value4/value5/");
        $this->assertEquals($params, array("param1" => "value1", "param2" => "value2", "param3" => "value3",
            "param4" => "value4", "param5" => "value5"));
    }

    public function testParseCustom()
    {
        $url = "/foo/1/2/3";
        $route = $this->object->parseCustom($url);
        $this->assertEquals($route,
            new RoutingObject($url, "Foobar", "index", array("bar" => 1, "baz" => 2, "blah" => 3)));

        $url = "/greetings/bonjour/galuh/fr.html";
        $route = $this->object->parseCustom($url);
        $this->assertEquals($route,
            new RoutingObject($url, "Greetings", "show", array("hello" => "bonjour",
                "name" => "galuh", "country" => "fr")));

        $url = "/category/personal/read/123/lorem-ipsum-dolor-sit-amet.html";
        $route = $this->object->parseCustom($url);
        $this->assertEquals($route,
            new RoutingObject($url, "Blog", "read", array("category" => "personal", "id" => 123,
                "slug" => "lorem-ipsum-dolor-sit-amet")));
    }
}
?>
