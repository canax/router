<?php

namespace Anax\Route;

use Anax\Route\Exception\NotFoundException;
use PHPUnit\Framework\TestCase;

/**
 * Try controller handlers that fails.
 */
class RouteHandlerControllerFailTest extends TestCase
{
    /**
     * Too few arguments.
     *
     * @expectedException Anax\Route\Exception\NotFoundException
     */
    public function testToFewArguments()
    {
        $route = new Route();
        $route->set(null, "user", null, "Anax\Route\MockHandlerController");
        $path = "user/view";
        $this->assertTrue($route->match($path, "GET"));
        $route->handle($path);
    }



    /**
     * Typed arguments as integer.
     *
     * @expectedException Anax\Route\Exception\NotFoundException
     */
    public function testTypedArgumentsInteger()
    {
        $route = new Route();
    
        $route->set(null, "user", null, "Anax\Route\MockHandlerController");

        $path = "user/view/a";
        $this->assertTrue($route->match($path, "GET"));
        $route->handle($path);
    }
}
