<?php
/**
 * Default route to create a 404, use if no else route matched, use this
 * as the last route, when all other routes fail.
 */
global $di;
$router = $di->get("router");

// A custom 404 handler
$router->always(function() use ($di) {
    return ["Anax 404: Not Found", 404];
}, "Catch all and send 404.");

// // Show all routes
// echo "ALL ROUTES\n";
// foreach ($router->getAll() as $route) {
//     echo $route->getAbsolutePath() . " : ";
//     echo $route->getRequestMethod() . " : ";
//     echo $route->getInfo() . "\n";
// }
// 
// // Show all internal routes
// echo "INTERNAL ROUTES\n";
// foreach ($router->getInternal() as $route) {
//     echo $route->getAbsolutePath() . " : ";
//     echo $route->getInfo() . "\n";
// }
