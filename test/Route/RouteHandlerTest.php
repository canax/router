<?php

namespace Anax\Route;

use \PHPUnit\Framework\TestCase;

/**
 * Try various type of handlers.
 */
class RouteHandlerTest extends TestCase
{
    /**
     * A handler can be a callable anonymous function.
     */
    public function testHandlerIsCallableAnonymousFunction()
    {
        $route = new Route();

        $route->set(null, null, null, function () {
            return "handler";
        });

        $res = $route->handle();
        $this->assertEquals("handler", $res);
    }



    /**
     * A handler can be a callable function, stored in a variable.
     */
    public function testHandlerIsCallableFunctionFromVariable()
    {
        $route = new Route();

        $function = function () {
            return "handler";
        };

        $route->set(null, null, null, $function);

        $res = $route->handle();
        $this->assertEquals("handler", $res);
    }



    /**
     * A handler can be a callable function, referenced by string.
     */
    public function testHandlerIsCallableFunctionFromString()
    {
        $route = new Route();

        $route->set(null, null, null, "handler");

        $res = $route->handle();
        $this->assertEquals("handler", $res);
    }



    /**
     * A handler can be a class method used as an object.
     */
    public function testHandlerIsCallableClassMethodAsObject()
    {
        $route = new Route();

        $object = new MockHandlerClassMethod();

        $route->set(null, null, null, [$object, "method"]);
        $res = $route->handle();
        $this->assertEquals("handler", $res);
    }



    /**
     * A handler can be a class method used as "object callable".
     */
    public function testHandlerIsCallableClassMethodObjectCallable()
    {
        $route = new Route();

        $route->set(null, null, null, ["Anax\Route\MockHandlerClassMethod", "method"]);
        $res = $route->handle();
        $this->assertEquals("handler", $res);
    }



    /**
     * A handler can be a static class method.
     */
    public function testHandlerIsCallableClassMethodAsStaticMethod()
    {
        $route = new Route();

        $route->set(null, null, null, ["Anax\Route\MockHandlerClassMethod", "static"]);
        $res = $route->handle();
        $this->assertEquals("handler", $res);
    }



    /**
     * A handler can be null.
     */
    public function testHandlerIsNull()
    {
        $route = new Route();

        $route->set(null, null, null, null);
        $res = $route->handle();
        $this->assertNull($res);
    }
}
