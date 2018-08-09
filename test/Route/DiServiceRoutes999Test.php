<?php

namespace Anax\Route;

use Anax\DI\DIFactoryConfig;
use PHPUnit\Framework\TestCase;

/**
 * Try routes loaded in $di configuration.
 */
class DiServiceRoutes999Test extends TestCase
{
    /**
     * Default route for not found.
     */
    public function testNotFoundRoute()
    {
        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/test/config/di_empty_router.php");
    
        $router = $di->get("router");
        $this->assertInstanceOf(Router::class, $router);

        $router->addRoutes(require ANAX_INSTALL_PATH . "/config/router/999_404.php");

        $res = $router->handle("");
        $this->assertEquals(2, count($res));
        $this->assertEquals("Anax 404: Not Found", $res[0]);
        $this->assertEquals(404, $res[1]);
    }
}
