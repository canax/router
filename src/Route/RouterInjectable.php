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
     * @param mixed  $action null, string or callable to implement a
     *                       controller for the route
     *
     * @return class as new route
     */
    public function add($rule, $action = null)
    {
        return $this->any(null, $rule, $action);
    }



    /**
     * Add aroute to the router with specific request method.
     *
     * @param array  $method as array of strings of request methods
     * @param string $rule   for this route
     * @param mixed  $action null, string or callable to implement a
     *                       controller for the route
     *
     * @return class as new route
     */
    public function any($method, $rule, $action = null)
    {
        $route = new Route();
        $route->set($rule, $action, $method);
        $this->routes[] = $route;

        return $route;
    }



    /**
     * Add a GET route to the router.
     *
     * @param string $rule   for this route
     * @param mixed  $action null, string or callable to implement a
     *                       controller for the route
     *
     * @return class as new route
     */
    public function get($rule, $action = null)
    {
        return $this->any(["GET"], $rule, $action);
    }



    /**
     * Add a POST route to the router.
     *
     * @param string $rule   for this route
     * @param mixed  $action null, string or callable to implement a
     *                       controller for the route
     *
     * @return class as new route
     */
    public function post($rule, $action = null)
    {
        return $this->any(["POST"], $rule, $action);
    }



    /**
     * Add a PUT route to the router.
     *
     * @param string $rule   for this route
     * @param mixed  $action null, string or callable to implement a
     *                       controller for the route
     *
     * @return class as new route
     */
    public function put($rule, $action = null)
    {
        return $this->any(["PUT"], $rule, $action);
    }



    /**
     * Add a DELETE route to the router.
     *
     * @param string $rule   for this route
     * @param mixed  $action null, string or callable to implement a
     *                       controller for the route
     *
     * @return class as new route
     */
    public function delete($rule, $action = null)
    {
        return $this->any(["DELETE"], $rule, $action);
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
     * Several routes can match and if the routehandler does not break
     * execution flow, the route matching will carry on.
     * Only the last routehandler will get its return value returned further.
     *
     * @param string $query   the query/route to match a handler for.
     * @param string $method  the request method to match.
     *
     * @return mixed content returned from route.
     */
    public function handle($query, $method = null)
    {
        try {
            $match = false;
            foreach ($this->routes as $route) {
                if ($route->match($query, $method)) {
                    $this->lastRoute = $route->getRule();
                    $match = true;
                    $results = $route->handle();
                }
            }

            if ($match) {
                return $results;
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
