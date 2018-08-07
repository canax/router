<?php
/**
 * Internal routes for error handling to show response when internal
 * exceptions are thrown.
 */
return [
    // Path where to mount the routes, is added to each route path.
    "mount" => null,

    // All routes in order
    "routes" => [
        [
            "info" => "403 Forbidden.",
            "internal" => true,
            "path" => "403",
            "handler" => function () {
                http_response_code(403);
                die("Anax 403: Forbidden");
            },
        ],
        [
            "info" => "404 Page not found.",
            "internal" => true,
            "path" => "404",
            "handler" => function () {
                http_response_code(404);
                die("Anax 404: Not Found");
            },
        ],
        [
            "info" => "500 Internal Server Error.",
            "internal" => true,
            "path" => "500",
            "handler" => function () {
                http_response_code(500);
                die("Anax 500: Internal Server Error");
            },
        ],
    ]
];
