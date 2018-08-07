<?php

namespace Anax\Route;

use Anax\DI\DIFactoryConfig;
use Anax\Configure\Configuration;
use PHPUnit\Framework\TestCase;

/**
 * Try that a DI service can be created from the di config file.
 */
class RouterAsDiServiceTest extends TestCase
{
    /**
     * Create the service.
     */
    public function testCreateDiService()
    {
        $cfg = new Configuration();
        $cfg->setBaseDirectories([ANAX_INSTALL_PATH . "/config"]);

        $di = new DIFactoryConfig();
        $di->loadServices(ANAX_INSTALL_PATH . "/config/di");
        $di->set("configuration", $cfg);

        $router = $di->get("router");
        $this->assertInstanceOf(Router::class, $router);
    }
}
