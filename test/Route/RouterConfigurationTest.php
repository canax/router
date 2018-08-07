<?php

namespace Anax\Route;

use PHPUnit\Framework\TestCase;
use Anax\Route\Exception\ConfigurationException;

/**
 * Test configuration of the router.
 */
class RouterConfigurationTest extends TestCase
{
    /**
     * Overwrite definition of mount.
     */
    public function testConfigOverwriteMountDefinition()
    {
        $router = new Router();
        $router->addRoutes([
            "mount" => "somewhere",
            "routes" => [
                [
                    "mount" => "mount",
                    "path" => "path",
                    "handler" => function () {
                        return "mount/path";
                    }
                ]
            ]
        ]);
        $res = $router->handle("mount/path");
        $this->assertEquals("mount/path", $res);
    }
}
