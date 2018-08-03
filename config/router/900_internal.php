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
            "handler" => ["errorController", "page403"],
        ],
        [
            "info" => "404 Page not found.",
            "internal" => true,
            "path" => "404",
            "handler" => ["errorController", "page404"],
        ],
        [
            "info" => "500 Internal Server Error.",
            "internal" => true,
            "path" => "500",
            "handler" => ["errorController", "page500"],
        ],
    ]
];
