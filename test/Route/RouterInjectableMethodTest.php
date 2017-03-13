<?php

namespace Anax\Route;

/**
 * Routes.
 *
 */
class RouterInjectableMethodTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     *
     * @return void
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

        $router->add("about", function () {
            return "about";
        });

        $res = $router->handle("about", "GET");
        $this->assertEquals("GET about", $res);

        $res = $router->handle("about", "POST");
        $this->assertEquals("POST about", $res);

        $res = $router->handle("about", "PUT");
        $this->assertEquals("PUT about", $res);

        $res = $router->handle("about", "DELETE");
        $this->assertEquals("DELETE about", $res);

        $res = $router->handle("about");
        $this->assertEquals("about", $res);
    }



    /**
     * Test
     *
     * @return void
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

        $router->add("about", function () {
            return "about";
        });

        $res = $router->handle("about", "GET");
        $this->assertEquals("GET/POST about", $res);

        $res = $router->handle("about", "POST");
        $this->assertEquals("GET/POST about", $res);

        $res = $router->handle("about", "PUT");
        $this->assertEquals("PUT/DELETE about", $res);

        $res = $router->handle("about", "DELETE");
        $this->assertEquals("PUT/DELETE about", $res);

        $res = $router->handle("about");
        $this->assertEquals("about", $res);
    }



    /**
     * Test
     *
     * @return void
     */
    public function testRequestMethodDefault()
    {
        $router = new RouterInjectable();

        $router->add("about", function () {
            return "about";
        });

        $router->any(["GET", "POST", "PUT", "DELETE"], "about", function () {
            return "any about";
        });

        $res = $router->handle("about", "GET");
        $this->assertEquals("about", $res);

        $res = $router->handle("about", "POST");
        $this->assertEquals("about", $res);

        $res = $router->handle("about", "PUT");
        $this->assertEquals("about", $res);

        $res = $router->handle("about", "DELETE");
        $this->assertEquals("about", $res);

        $res = $router->handle("about");
        $this->assertEquals("about", $res);
    }
}
