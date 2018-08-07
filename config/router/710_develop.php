<?php
/**
 * Routes to ease development and debugging.
 */
return [
    // Path where to mount the routes, is added to each route path.
    "mount" => "develop",

    // All routes in order
    "routes" => [
        [
            "info" => "Development and debugging information.",
            "method" => null,
            "path" => "devel",
            "handler" => ["debugController", "info"],
        ],
    ]
];
