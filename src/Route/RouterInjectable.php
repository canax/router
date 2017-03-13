<?php

namespace Anax\Route;

/**
 * A container for routes.
 *
 */
class RouterInjectable
{
    /**
     * Properties
     *
     */
    private $routes         = [];    // All the routes
    private $internalRoutes = [];    // All internal routes
    private $defaultRoute   = null;  // A default route to catch all
    private $lastRoute      = null;  // Last route that was callbacked



    /**
     * Get all routes.
     *
     * @return array with all routes.
     */
    public function getAll()
    {
        return $this->routes;
    }



    /**
     * Get all internal routes.
     *
     * @return array with internal routes.
     */
    public function getInternal()
    {
        return $this->internalRoutes;
    }



    /**
     * Add a route to the router.
     *
     * @param string $rule   for this route
     * @param mixed  $action null, string or callable to implement a controller for the route
     *
     * @return class as new route
     */
    public function add($rule, $action = null)
    {
        $route = new Route();
        $route->set($rule, $action);
        $this->routes[] = $route;

        // Set as default route
        if ($rule == "*") {
            $this->defaultRoute = $route;
        }

        return $route;
    }



    /**
     * Add a default route to the router, to use when all other routes fail.
     *
     * @param mixed  $action null, string or callable to implement a controller for the route
     *
     * @return class as new route
     */
    public function addDefault($action)
    {
        $route = new Route();
        $route->set("*", $action);
        $this->routes[] = $route;
        $this->defaultRoute = $route;

        return $route;
    }



    /**
     * Add an internal (not exposed to url-matching) route to the router.
     *
     * @param string $rule   for this route
     * @param mixed  $action null, string or callable to implement a controller for the route
     *
     * @return class as new route
     */
    public function addInternal($rule, $action = null)
    {
        $route = new Route();
        $route->set($rule, $action);
        $this->internalRoutes[$rule] = $route;
        return $route;
    }



    /**
     * Add an internal (not exposed to url-matching) route to the router.
     *
     * @param string $rule   for this route
     * @param mixed  $action null, string or callable to implement a
     *                       controller for the route
     *
     * @return void
     */
    public function handleInternal($rule)
    {
        if (!isset($this->internalRoutes[$rule])) {
            throw new NotFoundException("No internal route to handle: " . $rule);
        }
        $route = $this->internalRoutes[$rule];
        $this->lastRoute = $rule;
        $route->handle();
    }



    /**
     * Get the route for the last route that was handled.
     *
     * @return mixed
     */
    public function getLastRoute()
    {
        return $this->lastRoute;
    }



    /**
     * Handle the routes and match them towards the request, dispatch them
     * when a match is made. Each route handler may throw exceptions that
     * may redirect to an internal route for error handling.
     *
     * @param string $query   the query/route to match a handler for.
     *
     * @return mixed content returned from route.
     */
    public function handle($query)
    {
        try {
            // Match predefined routes
            foreach ($this->routes as $route) {
                if ($route->match($query)) {
                    $this->lastRoute = $route->getRule();
                    return $route->handle();
                }
            }

            // Use the "catch-all" route
            if ($this->defaultRoute) {
                $this->lastRoute = $route->getRule();
                return $this->defaultRoute->handle();
            }

            // No route was matched
            $this->handleInternal("404");
        } catch (ForbiddenException $e) {
            $this->handleInternal("403");
        } catch (NotFoundException $e) {
            $this->handleInternal("404");
        } catch (InternalErrorException $e) {
            $this->handleInternal("500");
        }
    }
}
