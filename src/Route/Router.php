<?php

namespace Anax\Route;

use Anax\DI\InjectionAwareInterface;
use Anax\DI\InjectionAwareTrait;
use Anax\Route\Exception\ForbiddenException;
use Anax\Route\Exception\NotFoundException;
use Anax\Route\Exception\InternalErrorException;
use Anax\Route\Exception\ConfigurationException;

/**
 * A container for routes.
 */
class Router implements
    InjectionAwareInterface
{
    use InjectionAwareTrait;



    /**
     * @var array       $routes         all the routes.
     * @var array       $internalRoutes all internal routes.
     * @var null|string $lastRoute      last route that was matched and called.
     */
    private $routes         = [];
    private $internalRoutes = [];
    private $lastRoute      = null;



    /**
     * @const DEVELOPMENT Verbose with exceptions.
     * @const PRODUCTION  Exceptions turns into 500.
     */
    const DEVELOPMENT = 0;
    const PRODUCTION  = 1;



    /**
     * @var integer $mode current mode.
     */
    private $mode = self::DEVELOPMENT;



    /**
     * Set Router::DEVELOPMENT or Router::PRODUCTION mode.
     *
     * @param integer $mode which mode to set.
     *
     * @return self to enable chaining.
     */
    public function setMode(integer $mode) : object
    {
        $this->mode = $mode;
        return $this;
    }



    /**
     * Add routes from an array where the array looks like this:
     * [
     *      "mount" => null|string, // Where to mount the routes
     *      "routes" => [           // All routes in this array
     *          [
     *              "info" => "Just say hi.",
     *              "method" => null,
     *              "path" => "hi",
     *              "handler" => function () {
     *                  return "Hi.";
     *              },
     *          ]
     *      ]
     * ]
     *
     * @throws ConfigurationException
     *
     * @param array $routes containing the routes to add.
     *
     * @return self to enable chaining.
     */
    public function addRoutes(array $routes) : object
    {
        $mount = null;
        if (isset($routes["mount"])) {
            $mount = rtrim($routes["mount"], "/");
            if (!empty($mount)) {
                $mount .= "/";
            }
        }

        if (!(isset($routes["routes"]) && is_array($routes["routes"]))) {
            throw new ConfigurationException(t("No routes found, missing key 'routes' in configuration array."));
        }

        foreach ($routes["routes"] as $route) {
            if ($route["internal"] ?? false) {
                $this->addInternalRoute(
                    $route["path"],
                    $route["handler"] ?? null
                );
                continue;
            }

            if (!array_key_exists("path", $route)) {
                throw new ConfigurationException(t("Creating route but path is not defined for route."));
            }

            $this->addRoute(
                $route["method"] ?? null,
                $mount . $route["path"],
                $route["handler"] ?? null,
                $route["info"] ?? null
            );
        }

        return $this;
    }



    /**
     * Add a route with a request method, a path rule to match and an action
     * as the callback. Adding several path rules (array) results in several
     * routes being created.
     *
     * @param string|array           $method  as request method to support
     * @param string                 $path    for this route
     * @param string|array|callable  $handler for this path, callable or equal
     * @param string                 $info    description of the route
     *
     * @return void.
     */
    protected function addRoute(
        $method,
        string $path = null,
        $handler = null,
        string $info = null
    ) : void
    {
        $route = new Route();
        $route->set($method, $path, $handler, $info);
        $this->routes[] = $route;
    }



    /**
     * Add an internal route to the router, this route is not exposed to the
     * browser and the end user.
     *
     * @param string                 $path    for this route
     * @param string|array|callable  $handler for this path, callable or equal
     *
     * @return void.
     */
    public function addInternalRoute(string $path, $handler) : void
    {
        $route = new Route();
        $route->set($path, $handler);
        $this->internalRoutes[$path] = $route;
    }



    /**
     * Load route from an array contining route details.
     *
     * @throws ConfigurationException
     *
     * @param array $route details on the route.
     *
     * @return self
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    public function load(array $route) : object
    {
        var_dump($route);
        
        $mount = isset($route["mount"]) ? rtrim($route["mount"], "/") : null;

        $config = $route;

        // Include the config file and load its routes
        $config = require($file);
        $routes = isset($config["routes"]) ? $config["routes"] : [];
        foreach ($routes as $route) {
            $path = isset($mount)
                ? $mount . "/" . $route["path"]
                : $route["path"];

            if (isset($route["internal"]) && $route["internal"]) {
                $this->addInternal($path, $route["callable"]);
                continue;
            }

            $this->any(
                $route["requestMethod"],
                $path,
                $route["callable"],
                $route["info"]
            );
        }

        return $this;
    }



    /**
     * Load and apply configurations.
     *
     * @param array|string $what is an array with key/value config options
     *                           or a file to be included which returns such
     *                           an array.
     *
     * @return self
     */
    public function configure($what)
    {
        $this->configure2($what);
        $includes = $this->getConfig("routeFiles", []);
        $items    = $this->getConfig("items", []);
        $config = array_merge($includes, $items);

        // Add a sort field if missing, to maintain order
        // when sorting
        $sort = 1;
        array_walk($config, function (&$item) use (&$sort) {
            $item["sort"] = (isset($item["sort"]))
                ? $item["sort"]
                : $sort++;
        });
        uasort($config, function ($item1, $item2) {
            if ($item1["sort"] === $item2["sort"]) {
                return 0;
            }
            return ($item1["sort"] < $item2["sort"]) ? -1 : 1;
        });

        foreach ($config as $route) {
            $this->load($route);
        }

        return $this;
    }



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
                    $results = $route->handle($this->di);
                    if ($results) {
                        return $results;
                    }
                }
            }

            $this->handleInternal("404");
        } catch (ForbiddenException $e) {
            $this->handleInternal("403");
        } catch (NotFoundException $e) {
            $this->handleInternal("404");
        } catch (InternalErrorException $e) {
            $this->handleInternal("500");
        } catch (ConfigurationException $e) {
            if ($this->mode === Router::PRODUCTION) {
                $this->handleInternal("500");
            }
            throw $e;
        }
    }



    /**
     * Handle an internal route, the internal routes are not exposed to the
     * end user.
     *
     * @param string $rule for this route.
     *
     * @throws \Anax\Route\Exception\NotFoundException
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
        $route->handle($this->di);
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
