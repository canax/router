<?php

namespace Anax\Route;

/**
 * Routes.
 */
class RouterInjectableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     */
    public function testRouter()
    {
        $router = new RouterInjectable();

        $router->add("about", function () {
            return "about";
        });

        $router->add("about/me", function () {
            return "about/me";
        });

        $res = $router->handle("about");
        $this->assertEquals("about", $res);

        $res = $router->handle("about/me");
        $this->assertEquals("about/me", $res);
    }



    /**
     * Test
     */
    public function testRouterWithSeveralMatches()
    {
        $router = new RouterInjectable();

        $router->add("", function () {
            echo "1";
        });

        $router->add("", function () {
            echo "2";
        });

        ob_start();
        $res = $router->handle("");
        $res = ob_get_contents();
        ob_end_clean();
        $this->assertEquals("12", $res);
    }



    /**
     * Test
     */
    public function testRouterOneStar()
    {
        $router = new RouterInjectable();

        $router->add("*", function () {
            return "*";
        });
        $res = $router->handle("some/route");
        $this->assertEquals("*", $res);
    }



    /**
     * Test
     */
    public function testRouterDoubleStar()
    {
        $router = new RouterInjectable();

        $router->add("**", function () {
            return "**";
        });
        $res = $router->handle("some/route");
        $this->assertEquals("**", $res);
    }



    /**
     * Test
     */
    public function testRouterForAll()
    {
        $router = new RouterInjectable();

        $router->all(null, function () {
            return "all";
        });
        $res = $router->handle("some/route");
        $this->assertEquals("all", $res);
    }
}
