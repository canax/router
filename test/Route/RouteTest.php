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
        
        $route->set('', null);
        $this->assertTrue($route->match(''));
        $this->assertFalse($route->match('-'));
    }



    /**
     * Test
     *
     * @return void
     */
    public function testDefaultRoute()
    {
        $route = new Route();

        $route->set('*', null);
        $this->assertTrue($route->match(''));
        $this->assertTrue($route->match('controller'));
        $this->assertTrue($route->match('controller/action'));
    }



    /**
     * Test
     *
     * @return void
     */
    public function testGeneralRoute()
    {
        $route = new Route();
        
        $route->set('doc', null);
        $this->assertFalse($route->match('doc/index'));
        $this->assertFalse($route->match('doc/index2'));
        $this->assertTrue($route->match('doc'));
        $this->assertFalse($route->match('do'));
        $this->assertFalse($route->match('docs'));
        $this->assertTrue($route->match('doc'));

        $route->set('doc/index', null);
        $this->assertFalse($route->match('doc'));
        $this->assertFalse($route->match('doc/inde'));
        $this->assertFalse($route->match('doc/indexx'));
        $this->assertTrue($route->match('doc/index'));
    }



    /**
     * Test
     *
     * @return void
     */
    public function testStarRoute()
    {
        $route = new Route();

        $route->set('doc/*', null);
        $this->assertFalse($route->match('docs'));
        $this->assertTrue($route->match('doc'));
        $this->assertTrue($route->match('doc/'));
        $this->assertTrue($route->match('doc/index'));
        $this->assertFalse($route->match('doc/index/index'));

        $route->set('doc/*/index', null);
        $this->assertFalse($route->match('doc'));
        $this->assertFalse($route->match('doc/index'));
        $this->assertFalse($route->match('doc/index/index1'));
        $this->assertTrue($route->match('doc/index/index'));
        $this->assertFalse($route->match('doc/index/index/index'));
    }



    /**
     * Test
     *
     * @return void
     */
    public function testRouteWithArguments()
    {
        $route = new Route();

        //
        $route->set('search/{arg1}', function ($arg1) {
            return $arg1;
        });

        $this->assertFalse($route->match('search'));
        $this->assertFalse($route->match('search/1/2'));
        $this->assertFalse($route->match('search/1/2/3'));

        $this->assertTrue($route->match('search/1'));
        $this->assertEquals("1", $route->handle());


        //
        $route->set('search/{arg1}/{arg2}', function ($arg1, $arg2) {
            return "$arg1$arg2";
        });

        $this->assertFalse($route->match('search'));
        $this->assertFalse($route->match('search/1'));
        $this->assertFalse($route->match('search/1/2/3'));

        $this->assertTrue($route->match('search/1/2'));
        $this->assertEquals("12", $route->handle());

        //
        $route->set('search/{arg1}/what/{arg2}', function ($arg1, $arg2) {
            return "$arg1$arg2";
        });

        $this->assertFalse($route->match('search'));
        $this->assertFalse($route->match('search/1/2'));
        $this->assertFalse($route->match('search/1/what'));
        $this->assertFalse($route->match('search/1/what/2/3'));
        $this->assertFalse($route->match('search/1/2/3'));

        $this->assertTrue($route->match('search/1/what/2'));
        $this->assertEquals("12", $route->handle());
    }
}
