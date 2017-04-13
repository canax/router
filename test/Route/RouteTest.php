<?php

namespace Anax\Route;

/**
 * Routes.
 *
 */
class RouteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     */
    public function testHomeRoute()
    {
        $route = new Route();
        
        $route->set("", null);
        $this->assertTrue($route->match(""));
        $this->assertFalse($route->match("-"));
    }



    /**
     * Test
     */
    public function testDefaultRoute()
    {
        $route = new Route();

        $route->set("*", null);
        $this->assertTrue($route->match(""));
        $this->assertTrue($route->match("controller"));
        $this->assertTrue($route->match("controller/action"));
    }



    /**
     * Test
     */
    public function testGeneralRoute()
    {
        $route = new Route();
        
        $route->set("doc", null);
        $this->assertFalse($route->match("doc/index"));
        $this->assertFalse($route->match("doc/index2"));
        $this->assertTrue($route->match("doc"));
        $this->assertFalse($route->match("do"));
        $this->assertFalse($route->match("docs"));
        $this->assertTrue($route->match("doc"));

        $route->set("doc/index", null);
        $this->assertFalse($route->match("doc"));
        $this->assertFalse($route->match("doc/inde"));
        $this->assertFalse($route->match("doc/indexx"));
        $this->assertTrue($route->match("doc/index"));
    }



    /**
     * Test
     */
    public function testStarRoute()
    {
        $route = new Route();

        $route->set("doc/*", null);
        $this->assertFalse($route->match("docs"));
        $this->assertTrue($route->match("doc"));
        $this->assertTrue($route->match("doc/"));
        $this->assertTrue($route->match("doc/index"));
        $this->assertFalse($route->match("doc/index/index"));

        $route->set("doc/*/index", null);
        $this->assertFalse($route->match("doc"));
        $this->assertFalse($route->match("doc/index"));
        $this->assertFalse($route->match("doc/index/index1"));
        $this->assertTrue($route->match("doc/index/index"));
        $this->assertFalse($route->match("doc/index/index/index"));
    }



    /**
     * Test
     */
    public function testDoubleStarRoute()
    {
        $route = new Route();

        $route->set("doc/**", null);
        $this->assertFalse($route->match("docs"));
        $this->assertTrue($route->match("doc"));
        $this->assertTrue($route->match("doc/"));
        $this->assertTrue($route->match("doc/index"));
        $this->assertTrue($route->match("doc/index/index"));

        $route->set("doc/**/index", null);
        $this->assertFalse($route->match("docs"));
        $this->assertTrue($route->match("doc"));
        $this->assertTrue($route->match("doc/index"));
        $this->assertTrue($route->match("doc/index/index1"));
        $this->assertTrue($route->match("doc/index/index"));
        $this->assertTrue($route->match("doc/index/index/index"));
    }



    /**
     * Test
     */
    public function testRequestMethod()
    {
        $route = new Route();

        $route->set("", null);
        $this->assertTrue($route->match(""));
        $this->assertTrue($route->match("", "GET"));
        $this->assertTrue($route->match("", "POST"));
        $this->assertTrue($route->match("", "PUT"));
        $this->assertTrue($route->match("", "DELETE"));

        $route->set("", null, ["GET"]);
        $this->assertFalse($route->match(""));
        $this->assertTrue($route->match("", "GET"));
        $this->assertFalse($route->match("", "POST"));
        $this->assertFalse($route->match("", "PUT"));
        $this->assertFalse($route->match("", "DELETE"));

        $route->set("", null, ["POST"]);
        $this->assertFalse($route->match(""));
        $this->assertFalse($route->match("", "GET"));
        $this->assertTrue($route->match("", "POST"));
        $this->assertFalse($route->match("", "PUT"));
        $this->assertFalse($route->match("", "DELETE"));

        $route->set("", null, ["PUT"]);
        $this->assertFalse($route->match(""));
        $this->assertFalse($route->match("", "GET"));
        $this->assertFalse($route->match("", "POST"));
        $this->assertTrue($route->match("", "PUT"));
        $this->assertFalse($route->match("", "DELETE"));

        $route->set("", null, ["DELETE"]);
        $this->assertFalse($route->match(""));
        $this->assertFalse($route->match("", "GET"));
        $this->assertFalse($route->match("", "POST"));
        $this->assertFalse($route->match("", "PUT"));
        $this->assertTrue($route->match("", "DELETE"));
    }



    /**
     * Test
     */
    public function testSettingRequestMethod()
    {
        $route = new Route();

        $route->set("", null, null);
        $this->assertTrue($route->match(""));

        $route->set("", null, "GET");
        $this->assertTrue($route->match("", "GET"));

        $route->set("", null, ["GET"]);
        $this->assertTrue($route->match("", "GET"));
    }



    /**
     * Test
     */
    public function testAddMethodAsString()
    {
        $route = new Route();

        $route->set("", null, "GET|POST");
        $this->assertFalse($route->match(""));
        $this->assertTrue($route->match("", "GET"));
        $this->assertTrue($route->match("", "POST"));
        $this->assertFalse($route->match("", "PUT"));
        $this->assertFalse($route->match("", "DELETE"));

        $route->set("", null, "GET|POST | PUT | DEL");
        $this->assertFalse($route->match(""));
        $this->assertTrue($route->match("", "GET"));
        $this->assertTrue($route->match("", "POST"));
        $this->assertTrue($route->match("", "PUT"));
        $this->assertFalse($route->match("", "DELETE"));

        $route->set("", null, "GET|POST | PUT | DELETE");
        $this->assertFalse($route->match(""));
        $this->assertTrue($route->match("", "GET"));
        $this->assertTrue($route->match("", "POST"));
        $this->assertTrue($route->match("", "PUT"));
        $this->assertTrue($route->match("", "DELETE"));
    }



    /**
     * Test
     */
    public function testNullRoute()
    {
        $route = new Route();

        $route->set(null, null, null);
        $this->assertTrue($route->match("whatever/any"));
        $this->assertTrue($route->match("whatever/any", "GET"));
        $this->assertTrue($route->match("whatever/any", "POST"));
        $this->assertTrue($route->match("whatever/any", "PUT"));
        $this->assertTrue($route->match("whatever/any", "DELETE"));

        $route->set(null, null, "GET|POST");
        $this->assertFalse($route->match("whatever/any"));
        $this->assertTrue($route->match("whatever/any", "GET"));
        $this->assertTrue($route->match("whatever/any", "POST"));
        $this->assertFalse($route->match("whatever/any", "PUT"));
        $this->assertFalse($route->match("whatever/any", "DELETE"));

        $route->set(null, null, "GET | POST | PUT | DELETE");
        $this->assertFalse($route->match("whatever/any"));
        $this->assertTrue($route->match("whatever/any", "GET"));
        $this->assertTrue($route->match("whatever/any", "POST"));
        $this->assertTrue($route->match("whatever/any", "PUT"));
        $this->assertTrue($route->match("whatever/any", "DELETE"));
    }
}
