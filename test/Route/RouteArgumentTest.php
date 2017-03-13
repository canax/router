<?php

namespace Anax\Route;

/**
 * Routes.
 *
 */
class RouteArgumentTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     *
     * @return void
     */
    public function testRouteWithArguments()
    {
        $route = new Route();

        //
        $route->set("search/{arg1}", function ($arg1) {
            return $arg1;
        });

        $this->assertFalse($route->match("search"));
        $this->assertFalse($route->match("search/1/2"));
        $this->assertFalse($route->match("search/1/2/3"));

        $this->assertTrue($route->match("search/1"));
        $this->assertEquals("1", $route->handle());


        //
        $route->set("search/{arg1}/{arg2}", function ($arg1, $arg2) {
            return "$arg1$arg2";
        });

        $this->assertFalse($route->match("search"));
        $this->assertFalse($route->match("search/1"));
        $this->assertFalse($route->match("search/1/2/3"));

        $this->assertTrue($route->match("search/1/2"));
        $this->assertEquals("12", $route->handle());

        //
        $route->set("search/{arg1}/what/{arg2}", function ($arg1, $arg2) {
            return "$arg1$arg2";
        });

        $this->assertFalse($route->match("search"));
        $this->assertFalse($route->match("search/1/2"));
        $this->assertFalse($route->match("search/1/what"));
        $this->assertFalse($route->match("search/1/what/2/3"));
        $this->assertFalse($route->match("search/1/2/3"));

        $this->assertTrue($route->match("search/1/what/2"));
        $this->assertEquals("12", $route->handle());
    }



    /**
     * Test
     *
     * @return void
     */
    public function testRouteArgumentValidate()
    {
        $route = new Route();
        $digit = "1234567890";
        $alpha = "abcdefghijklmnopqrstuvxyzABCDEFGHIJKLMNOPQRSTUVXYZ";
        $alphanum = $alpha . $digit;
        $hex = "abcdefABCDEF" . $digit;

        //
        $route->set("{arg1:digit}", null);
        $this->assertFalse($route->match($alpha));
        $this->assertFalse($route->match($alphanum));
        $this->assertTrue($route->match($digit));
        $this->assertFalse($route->match($hex));

        //
        $route->set("{arg1:alpha}", null);
        $this->assertTrue($route->match($alpha));
        $this->assertFalse($route->match($alphanum));
        $this->assertFalse($route->match($digit));
        $this->assertFalse($route->match($hex));

        //
        $route->set("{arg1:alphanum}", null);
        $this->assertTrue($route->match($alpha));
        $this->assertTrue($route->match($alphanum));
        $this->assertTrue($route->match($digit));
        $this->assertTrue($route->match($hex));

        //
        $route->set("{arg1:hex}", null);
        $this->assertFalse($route->match($alpha));
        $this->assertFalse($route->match($alphanum));
        $this->assertTrue($route->match($digit));
        $this->assertTrue($route->match($hex));
    }
}
