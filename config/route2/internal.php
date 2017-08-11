<?php
/**
 * Internal routes for error handling.
 */
$app->router->addInternal("403", function () use ($app) {
    $title = "403 Forbidden";
    $app->view->add("default1/http_status_code", [
        "title" => $title,
        "message" => "You are not permitted to do this.",
    ]);
    $app->renderPage([
        "title" => $title,
    ], 403);
});

$app->router->addInternal("404", function () use ($app) {
    $title = "404 Page not found";
    $app->view->add("default1/http_status_code", [
        "title" => "404 Page not found",
        "message" => "The page you are looking for is not here.",
    ]);
    $app->renderPage([
        "title" => $title,
    ], 404);
});

$app->router->addInternal("500", function () use ($app) {
    $title = "500 Internal Server Error";
    $app->view->add("default1/http_status_code", [
        "title" => "500 Internal Server Error",
        "message" => "An unexpected condition was encountered.",
    ]);
    $app->renderPage([
        "title" => $title,
    ], 500);
});
