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
                return ["Anax 403: Forbidden", 403];
            },
        ],
        [
            "info" => "404 Page not found.",
            "internal" => true,
            "path" => "404",
            "handler" => function () {
                return ["Anax 404: Not Found", 404];
            },
        ],
        [
            "info" => "500 Internal Server Error.",
            "internal" => true,
            "path" => "500",
            "handler" => function () {
                // echo "<pre>";
                // debug_print_backtrace();
                return ["Anax 500: Internal Server Error", 500];
            },
        ],
    ]
];
