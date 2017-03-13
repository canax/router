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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
     *
     * @return void
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
}
