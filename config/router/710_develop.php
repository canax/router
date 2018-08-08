<?php

use Anax\Route\Exception\NotFoundException;

/**
 * Routes to ease development and debugging.
 */
return [
    // Path where to mount the routes, is added to each route path.
    "mount" => "dev",

    // All routes in order
    "routes" => [
        [
            "info" => "Development and debugging information.",
            "method" => null,
            "path" => "*",
            "handler" => function ($di) {
                $title = " | Anax development utilities";
                $pages = [
                    "" => "index",
                    "di" => "di",
                    "request" => "request",
                    "router" => "router",
                    "session" => "session",
                    "view" => "view",
                ];

                $path = $di->get("router")->getMatchedPath();
                if (!array_key_exists($path, $pages)) {
                    throw new NotFoundException();
                }

                $page = $di->get("page");
                $page->add(
                    "anax/v2/dev/{$pages[$path]}",
                    [
                        "mount" => "dev/"
                    ]
                );

                return $page->render([
                    "title" => ucfirst($pages[$path]) . $title
                ]); 
            },
        ],
        [
            "info" => "Add +1 to session.",
            "path" => "session/increment",
            "handler" => function ($di) {
                $session = $di->get("session");
                $number = $session->get("number", 0);
                $session->set("number", $number + 1);
                var_dump($session);
                return "Reload page to increment 'number' in the session.";
            },
        ],
        [
            "info" => "Destroy the session.",
            "path" => "session/destroy",
            "handler" => function ($di) {
                $session = $di->get("session");
                var_dump($session);
                $session->destroy();
                var_dump($session);
                return "The session was destroyed.";
            },
        ],
    ]
];
