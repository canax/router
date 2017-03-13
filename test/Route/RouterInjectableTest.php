<?php

namespace Anax\Route;

/**
 * Routes.
 *
 */
class RouterInjectableTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     *
     * @return void
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
     *
     * @return void
     */
    public function testRouterDefault()
    {
        $router = new RouterInjectable();

        // One way to add default route
        $router->add("*", function () {
            return "*";
        });
        $res = $router->handle("some/route");
        $this->assertEquals("*", $res);
    }



    /**
     * Test
     *
     * @expectedException \Anax\Route\NotFoundException
     */
    public function testRouter404()
    {
        $router = new RouterInjectable();

        $router->handle("no-route");
    }



    /**
     * Test route handler throwing exceptions.
     *
     * @expectedException \Anax\Route\NotFoundException
     */
    public function testRouterNotFound()
    {
        $router = new RouterInjectable();

        $router->addInternal("404", function () {
            throw new NotFoundException();
        });

        $router->add("notfound", function () {
            throw new NotFoundException();
        });
        $router->handle("notfound");
    }



    /**
     * Test route handler throwing exceptions.
     *
     * @expectedException \Anax\Route\ForbiddenException
     */
    public function testRouterForbidden()
    {
        $router = new RouterInjectable();

        $router->addInternal("403", function () {
            throw new ForbiddenException();
        });

        $router->add("forbidden", function () {
            throw new ForbiddenException();
        });
        $router->handle("forbidden");
    }



    /**
     * Test route handler throwing exceptions.
     *
     * @expectedException \Anax\Route\InternalErrorException
     */
    public function testRouterInternalError()
    {
        $router = new RouterInjectable();

        $router->addInternal("500", function () {
            throw new InternalErrorException();
        });

        $router->add("internal/error", function () {
            throw new InternalErrorException();
        });
        $router->handle("internal/error");
    }
}
