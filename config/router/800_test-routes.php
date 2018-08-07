<?php

use Anax\Route\Exception\ForbiddenException;
use Anax\Route\Exception\NotFoundException;

/**
 * Routes to ease testing.
 */
return [
    // Path where to mount the routes, is added to each route path.
    "mount" => "test/router",

    // All routes in order
    "routes" => [
        [
            "info" => "Try internal 403.",
            "path" => "403",
            "handler" => function () {
                throw new ForbiddenException();
            },
        ],
        [
            "info" => "Try internal 404.",
            "path" => "404",
            "handler" => function () {
                throw new NotFoundException();
            },
        ],
        [
            "info" => "Try internal 500.",
            "path" => "500",
            "handler" => function () {
                throw new Exception();
            },
        ],
    ]
];
