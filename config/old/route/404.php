<?php
/**
 * Default route to create a 404, use if no else route matched.
 */
$app->router->always(function () use ($app) {
    throw new \Anax\Route\NotFoundException();
});
