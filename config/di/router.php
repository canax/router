<?php
/**
 * Configuration file for DI container.
 */
return [

    // Services to add to the container.
    "services" => [
        "router" => [
            "shared" => true,
            "callback" => function () {
                $router = new \Anax\Route\Router();
                $router->setDI($this);

                // Load the configuration files
                $cfg = $this->get("configuration");
                $config = $cfg->load("router");
                echo "ROUTER CONFIG";
                var_dump($config);

                // Add routes from configuration file
                $file = null;
                try {
                    $file = $config["file"];
                    $router->addRoutes($config["config"] ?? []);
                    foreach ($config["items"] as $routes) {
                        $file = $routes["file"];
                        $router->addRoutes($routes["config"]);
                    }
                } catch (Exception $e) {
                    throw new Exception(
                        $e->getMessage()
                        . t(
                            " Configuration file: '@file'",
                            ["@file" => $file]
                        )
                    );
                }

                // Set DEVELOPMENT/PRODUCTION mode, if defined
                if (isset($config["mode"])) {
                    $router->setMode($config["mode"]);
                } else if (defined("ANAX_PRODUCTION")) {
                    $router->setMode(\Anax\Route\Router::PRODUCTION);
                }

                return $router;
            }
        ],
    ],
];
