<?php

namespace Anax\Route;

/**
 * Routes.
 */
class RouterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Check that the route can return a value.
     */
    public function testRouter()
    {
        $router = new Router();

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
     * Check that all routes matching are called.
     */
    public function testRouterWithSeveralMatches()
    {
        $router = new Router();

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
     * Check that first matched route returning a value
     * is the returning value.
     */
    public function testReturnValueWithSeveralMatches()
    {
        $router = new Router();

        $router->add("about", function () {
            ;
        });

        $router->add("about", function () {
            return false;
        });

        $router->add("about", function () {
            return "yes";
        });

        $router->add("about", function () {
            return "no";
        });

        $res = $router->handle("about");
        $this->assertEquals("yes", $res);
    }



    /**
     * Check that "*" matches any route.
     */
    public function testRouterOneStar()
    {
        $router = new Router();

        $router->addInternal("404", function () {
            echo "404 ";
        });

        $router->add("about/*", function () {
            echo "about ";
        });

        ob_start();
        // try it out using some paths
        $router->handle("");
        $router->handle("about");
        $router->handle("about/me");
        $router->handle("about/you");
        $router->handle("about/some/other");
        $res = ob_get_contents();
        ob_end_clean();
        $this->assertEquals($res, "404 about about about 404 ");
    }



    /**
     * Check that "**" matches any route, including subpaths.
     */
    public function testRouterDoubleStar()
    {
        $router = new Router();

        $router->addInternal("404", function () {
            echo "404 ";
        });

        $router->add("about/**", function () {
            echo "about ";
        });

        ob_start();
        // try it out using some paths
        $router->handle("");
        $router->handle("about");
        $router->handle("about/me");
        $router->handle("about/you");
        $router->handle("about/some/other");
        $res = ob_get_contents();
        ob_end_clean();
        $this->assertEquals($res, "404 about about about about ");
    }



    /**
     * Check that the any route matches any/all routes.
     */
    public function testRouterForAll()
    {
        $router = new Router();

        $router->all(null, function () {
            return "all";
        });
        $res = $router->handle("some/route");
        $this->assertEquals("all", $res);
    }
}
