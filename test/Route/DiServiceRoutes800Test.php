<?php

namespace Anax\Route;

use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;

/**
 * Try routes loaded in $di configuration.
 */
class DiServiceRoutes800Test extends TestCase
{
    /**
     * Test a route.
     */
    public function testRouteHi()
    {
        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/test/config/di_empty_router.php");
    
        $router = $di->get("router");
        $this->assertInstanceOf(Router::class, $router);

        $router->addRoutes(require ANAX_INSTALL_PATH . "/config/router/800_test.php");

        $res = $router->handle("test/hi");
        $this->assertEquals("Hi.", $res);
    }



    /**
     * Test a route.
     */
    public function testRouteNo()
    {
        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/test/config/di_empty_router.php");
    
        $router = $di->get("router");
        $this->assertInstanceOf(Router::class, $router);

        $router->addRoutes(require ANAX_INSTALL_PATH . "/config/router/800_test.php");

        $res = $router->handle("test/no");
        $this->assertEquals(2, count($res));
        $this->assertEquals("No!", $res[0]);
        $this->assertEquals(500, $res[1]);
    }



    /**
     * Test a route.
     */
    public function testRouteJson()
    {
        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/test/config/di_empty_router.php");
    
        $router = $di->get("router");
        $this->assertInstanceOf(Router::class, $router);

        $router->addRoutes(require ANAX_INSTALL_PATH . "/config/router/800_test.php");

        $res = $router->handle("test/json");
        $this->assertEquals(1, count($res));
        $this->assertArraySubset(["message" => "Hi JSON"], $res[0]);
    }



    /**
     * Provider internal routes.
     */
    public function internalRoutesProvider()
    {
        return [
            ["test/403", "Anax 403: Forbidden", 403],
            ["test/404", "Anax 404: Not Found", 404],
            ["test/500", "Anax 500: Internal Server Error", 500],
        ];
    }



    /**
     * Test routes.
     *
     * @dataProvider internalRoutesProvider
     */
    public function testRouteInternals($path, $res1, $res2)
    {
        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/test/config/di_empty_router.php");
    
        $router = $di->get("router");
        $this->assertInstanceOf(Router::class, $router);

        $router->addRoutes(require ANAX_INSTALL_PATH . "/config/router/800_test.php");
        $router->addRoutes(require ANAX_INSTALL_PATH . "/config/router/900_internal.php");

        $res = $router->handle($path);
        $this->assertEquals(2, count($res));
        $this->assertEquals($res1, $res[0]);
        $this->assertEquals($res2, $res[1]);
    }
}
