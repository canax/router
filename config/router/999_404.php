<?php
/**
 * Default route to create a 404, use if no else route matched, use this
 * as the last route, when all other routes fail.
 */
return [
    "routes" => [
        [
            "info" => "Catch all and send 404.",
            "method" => null,
            "path" => null,
            "handler" => function () {
                return ["Anax 404: Not Found", 404];
            },
        ],
    ]
];
