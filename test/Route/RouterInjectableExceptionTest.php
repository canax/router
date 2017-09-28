<?php

namespace Anax\Route;

use \Anax\Route\Exception\ForbiddenException;
use \Anax\Route\Exception\InternalErrorException;
use \Anax\Route\Exception\NotFoundException;

/**
 * Routes.
 */
class RouterInjectableExceptionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test
     *
     * @expectedException \Anax\Route\Exception\NotFoundException
     */
    public function testRouter404()
    {
        $router = new RouterInjectable();

        $router->handle("no-route");
    }



    /**
     * Test route handler throwing exceptions.
     *
     * @expectedException \Anax\Route\Exception\NotFoundException
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
     * @expectedException \Anax\Route\Exception\ForbiddenException
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
     * @expectedException \Anax\Route\Exception\InternalErrorException
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
