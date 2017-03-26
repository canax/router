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
     * @param null|string          $rule   for this route
     * @param null|string|callable $action to implement a handler for the route
     *
     * @return class as new route
     */
    public function add($rule, $action)
    {
        return $this->any(null, $rule, $action);
    }



    /**
     * Add aroute to the router with specific request method.
     *
     * @param null|string|array    $method as request methods
     * @param null|string          $rule   for this route
     * @param null|string|callable $action to implement a handler for the route
     *
     * @return class as new route
     */
    public function any($method, $rule, $action)
    {
        $route = new Route();
        $route->set($rule, $action, $method);
        $this->routes[] = $route;

        return $route;
    }



    /**
     * Add a route to the router which will be applied for any route, if the
     * method is matching.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action to implement a handler for the route
     *
     * @return class as new route
     */
    public function all($method, $action = null)
    {
        return $this->any($method, null, $action);
    }



    /**
     * Add a GET route to the router.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action to implement a handler for the route
     *
     * @return class as new route
     */
    public function get($rule, $action)
    {
        return $this->any(["GET"], $rule, $action);
    }



    /**
     * Add a POST route to the router.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action to implement a handler for the route
     *
     * @return class as new route
     */
    public function post($rule, $action)
    {
        return $this->any(["POST"], $rule, $action);
    }



    /**
     * Add a PUT route to the router.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action to implement a handler for the route
     *
     * @return class as new route
     */
    public function put($rule, $action)
    {
        return $this->any(["PUT"], $rule, $action);
    }



    /**
     * Add a DELETE route to the router.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action to implement a handler for the route
     *
     * @return class as new route
     */
    public function delete($rule, $action)
    {
        return $this->any(["DELETE"], $rule, $action);
    }



    /**
     * Add an internal (not exposed to url-matching) route to the router.
     *
     * @param string               $rule   for this route
     * @param null|string|callable $action to implement a handler for the route
     *
     * @return class as new route
     */
    public function addInternal($rule, $action)
    {
        $route = new Route();
        $route->set($rule, $action);
        $this->internalRoutes[$rule] = $route;
        return $route;
    }



    /**
     * Handle an internal route.
     *
     * @param string $rule   for this route
     *
     * @return void
     *
     * @throws \Anax\Route\NotFoundException
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
