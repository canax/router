<?php

namespace Anax\Route;

/**
 * A mock handler as a controller.
 */
class MockHandlerControllerCatchAll
{
    public function catchAll(...$args)
    {
        return "catchAll";
    }
}
