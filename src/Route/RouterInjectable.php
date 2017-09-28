<?php

namespace Anax\Route;

use \Anax\Common\AppInjectableInterface;
use \Anax\Common\AppInjectableTrait;
use \Anax\Route\Exception\ForbiddenException;
use \Anax\Route\Exception\InternalErrorException;
use \Anax\Route\Exception\NotFoundException;

/**
 * A container for routes.
 */
class RouterInjectable implements AppInjectableInterface
{
    use AppInjectableTrait;

    /**
     * @var array       $routes         all the routes.
     * @var array       $internalRoutes all internal routes.
     * @var null|string $lastRoute      last route that was matched and called.
     */
    private $routes         = [];
    private $internalRoutes = [];
    private $lastRoute      = null;



    /**
     * Handle the routes and match them towards the request, dispatch them
     * when a match is made. Each route handler may throw exceptions that
     * may redirect to an internal route for error handling.
     * Several routes can match and if the routehandler does not break
     * execution flow, the route matching will carry on.
     * Only the last routehandler will get its return value returned further.
     *
     * @param string $path    the path to find a matching handler for.
     * @param string $method  the request method to match.
     *
     * @return mixed content returned from route.
     */
    public function handle($path, $method = null)
    {
        try {
            $match = false;
            foreach ($this->routes as $route) {
                if ($route->match($path, $method)) {
                    $this->lastRoute = $route->getRule();
                    $match = true;
                    $results = $route->handle($this->app);
                }
            }

            if ($match) {
                return $results;
            }

            $this->handleInternal("404");
        } catch (ForbiddenException $e) {
            $this->handleInternal("403");
        } catch (NotFoundException $e) {
            $this->handleInternal("404");
        } catch (InternalErrorException $e) {
            $this->handleInternal("500");
        }
    }



    /**
     * Handle an internal route, the internal routes are not exposed to the
     * end user.
     *
     * @param string $rule for this route.
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
     * Load routes from a config file, the file should return an array with
     * routes.
     *
     * @param string $file to load routes from.
     *
     * @return self
     */
    public function load($file)
    {
        $config = require $file;
        foreach ($config["routes"] as $route) {
            $this->any(
                $route["requestMethod"],
                $route["path"],
                $route["callable"]
            );
        }
        return $this;
    }



    /**
     * Add a route with a request method, a path rule to match and an action
     * as the callback. Adding several path rules (array) results in several
     * routes being created.
     *
     * @param null|string|array    $method as a valid request method.
     * @param null|string|array    $rule   path rule for this route.
     * @param null|string|callable $action to implement a handler for the route.
     *
     * @return class|array as new route(s), class if one added, else array.
     */
    public function any($method, $rule, $action)
    {
        $rules = is_array($rule) ? $rule : [$rule];

        $routes = [];
        foreach ($rules as $val) {
            $route = new Route();
            $route->set($val, $action, $method);
            $routes[] = $route;
            $this->routes[] = $route;
        }

        return count($routes) === 1 ? $routes[0] : $routes;
    }



    /**
     * Add a route to the router by rule(s) and a callback.
     *
     * @param null|string|array    $rule   for this route.
     * @param null|string|callable $action a callback handler for the route.
     *
     * @return class|array as new route(s), class if one added, else array.
     */
    public function add($rule, $action = null)
    {
        return $this->any(null, $rule, $action);
    }



    /**
    * Add a default route which will be applied for any path.
     *
     * @param string|callable $action a callback handler for the route.
     *
     * @return class as new route.
     */
    public function always($action)
    {
        return $this->any(null, null, $action);
    }



    /**
     * Add a default route which will be applied for any path, if the choosen
     * request method is matching.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action a callback handler for the route.
     *
     * @return class|array as new route(s), class if one added, else array.
     */
    public function all($method, $action)
    {
        return $this->any($method, null, $action);
    }



    /**
     * Shortcut to add a GET route.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action a callback handler for the route.
     *
     * @return class|array as new route(s), class if one added, else array.
     */
    public function get($rule, $action)
    {
        return $this->any(["GET"], $rule, $action);
    }



    /**
    * Shortcut to add a POST route.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action a callback handler for the route.
     *
     * @return class|array as new route(s), class if one added, else array.
     */
    public function post($rule, $action)
    {
        return $this->any(["POST"], $rule, $action);
    }



    /**
    * Shortcut to add a PUT route.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action a callback handler for the route.
     *
     * @return class|array as new route(s), class if one added, else array.
     */
    public function put($rule, $action)
    {
        return $this->any(["PUT"], $rule, $action);
    }



    /**
    * Shortcut to add a DELETE route.
     *
     * @param null|string|array    $method as request methods
     * @param null|string|callable $action a callback handler for the route.
     *
     * @return class|array as new route(s), class if one added, else array.
     */
    public function delete($rule, $action)
    {
        return $this->any(["DELETE"], $rule, $action);
    }



    /**
     * Add an internal route to the router, this route is not exposed to the
     * browser and the end user.
     *
     * @param string               $rule   for this route
     * @param null|string|callable $action a callback handler for the route.
     *
     * @return class|array as new route(s), class if one added, else array.
     */
    public function addInternal($rule, $action)
    {
        $route = new Route();
        $route->set($rule, $action);
        $this->internalRoutes[$rule] = $route;
        return $route;
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
}
