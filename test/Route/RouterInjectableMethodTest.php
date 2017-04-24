<?php

namespace Anax\Route;

/**
 * Testcases.
 */
class RouterInjectableMethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test shortcuts for adding route with specific request methdod.
     */
    public function testRequestMethods()
    {
        $router = new RouterInjectable();

        $router->get("about", function () {
            return "GET about";
        });

        $router->post("about", function () {
            return "POST about";
        });

        $router->put("about", function () {
            return "PUT about";
        });

        $router->delete("about", function () {
            return "DELETE about";
        });

        $res = $router->handle("about", "GET");
        $this->assertEquals("GET about", $res);

        $res = $router->handle("about", "POST");
        $this->assertEquals("POST about", $res);

        $res = $router->handle("about", "PUT");
        $this->assertEquals("PUT about", $res);

        $res = $router->handle("about", "DELETE");
        $this->assertEquals("DELETE about", $res);
    }



    /**
     * Test adding request methods usind any().
     */
    public function testRequestMethodAny()
    {
        $router = new RouterInjectable();

        $router->any(["GET", "POST"], "about", function () {
            return "GET/POST about";
        });

        $router->any(["PUT", "DELETE"], "about", function () {
            return "PUT/DELETE about";
        });

        $res = $router->handle("about", "GET");
        $this->assertEquals("GET/POST about", $res);

        $res = $router->handle("about", "POST");
        $this->assertEquals("GET/POST about", $res);

        $res = $router->handle("about", "PUT");
        $this->assertEquals("PUT/DELETE about", $res);

        $res = $router->handle("about", "DELETE");
        $this->assertEquals("PUT/DELETE about", $res);
    }



    /**
     * Test variants of default handlers independent of request method.
     */
    public function testRequestMethodDefault()
    {
        $router = new RouterInjectable();

        $remember = "";

        $router->add("about", function () use (&$remember) {
            $remember = "about ";
            return $remember;
        });

        $router->any(
            ["GET", "POST", "PUT", "DELETE"],
            "about",
            function () use (&$remember) {
                $remember .= "any about";
                return $remember;
            }
        );

        $res = $router->handle("about", "GET");
        $this->assertEquals("about any about", $res);

        $res = $router->handle("about", "POST");
        $this->assertEquals("about any about", $res);

        $res = $router->handle("about", "PUT");
        $this->assertEquals("about any about", $res);

        $res = $router->handle("about", "DELETE");
        $this->assertEquals("about any about", $res);

        $res = $router->handle("about");
        $this->assertEquals("about ", $res);
    }



    /**
     * Test adding singel path rule or array of rules.
     */
    public function testAddingSinglePathRule()
    {
        $router = new RouterInjectable();

        // add as string
        $routes = $router->add(
            "info",
            function () {
                return "info";
            }
        );

        $this->assertTrue(is_object($routes));

        // add as single in array
        $routes = $router->add(
            ["info"],
            function () {
                return "info";
            }
        );

        $this->assertTrue(is_object($routes));

        // add multiple in array
        $routes = $router->add(
            ["info", "info1/hej", "info2/{arg}"],
            function ($arg = null) {
                return "info $arg";
            }
        );

        $this->assertTrue(is_array($routes));
    }



    /**
     * Test adding array of path rules to extend to several routes.
     */
    public function testAddingArrayOfPathRules()
    {
        $router = new RouterInjectable();

        $router->add(
            ["info", "info1/hej", "info2/{arg}"],
            function ($arg = null) {
                return "info $arg";
            }
        );

        $res = $router->handle("info");
        $this->assertEquals("info ", $res);

        $res = $router->handle("info1/hej");
        $this->assertEquals("info ", $res);

        $res = $router->handle("info2/hej2");
        $this->assertEquals("info hej2", $res);
    }
}
