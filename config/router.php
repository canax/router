<?php

use Anax\Route\Router;

/**
 * Configuration file for routes.
 */
return [
    //"mode" => Router::DEVELOPMENT, // default, verbose execeptions
    //"mode" => Router::PRODUCTION,  // exceptions turn into 500

    // Path where to mount the routes, is added to each route path.
    "mount" => null,

    // Load routes in order, start with these and the those found in
    // router/*.php.
    "routes" => [
        [
            "info" => "Just say hi.",
            "method" => null,
            "path" => "",
            "handler" => function () {
                echo "200";
                return "200";
            },
        ],
        // [
        //     // For debugging and development details on Anax
        //     "mount" => "debug",
        //     "file" => __DIR__ . "/route2/debug.php",
        // ],
        // [
        //     // To read flat file content in Markdown from content/
        //     "mount" => null,
        //     "file" => __DIR__ . "/route2/flat-file-content.php",
        // ],
        // [
        //     // Keep this last since its a catch all
        //     "mount" => null,
        //     "sort" => 999,
        //     "file" => __DIR__ . "/route2/404.php",
        // ],
    ],

];
