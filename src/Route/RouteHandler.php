<?php

namespace Anax\Route;

use Anax\Commons\ContainerInjectableInterface;
use Anax\Route\Exception\ConfigurationException;
use Anax\Route\Exception\NotFoundException;

/**
 * Call a routes handler and return the results.
 */
class RouteHandler
{
    /**
     * Handle the action for a route and return the results.
     *
     * @param string                       $method    the request method.
     * @param string                       $path      that was matched.
     * @param string|array                 $action    base for the callable.
     * @param array                        $arguments optional arguments.
     * @param ContainerInjectableInterface $di        container with services.
     *
     * @return mixed as the result from the route handler.
     */
    public function handle(
        string $method = null,
        string $path = null,
        $action,
        array $arguments = [],
        ContainerInterface $di = null
    ) {
        if (is_null($action)) {
            return;
        }

        if (is_callable($action)) {
            return $this->handleAsCallable($action, $arguments);
        }

        if (is_string($action) && class_exists($action)) {
            $callable = $this->isControllerAction($method, $path, $action);
            if ($callable) {
                return $this->handleAsControllerAction($callable);
            }
        }

        // if ($di
        //     && is_array($action)
        //     && isset($action[0])
        //     && isset($action[1])
        //     && is_string($action[0])
        // ) {
        //     // Try to load service from app/di injected container
        //     return $this->handleUsingDi($action, $arguments, $di);
        // }
        //
        throw new ConfigurationException("Handler for route does not seem to be a callable action.");
    }



    /**
     * Check if items can be used to call a controller action, verify
     * that the controller exists, the action has a class-method to call.
     *
     * @param string $method the request method.
     * @param string $path   the matched path, base for the controller action
     *                       and the arguments.
     * @param string $class  the controller class
     *
     * @return array with callable details.
     */
    protected function isControllerAction(
        string $method = null,
        string $path = null,
        string $class
    ) {
        $args = explode("/", $path);
        $action = array_shift($args);
        $action = empty($action) ? "index" : $action;
        $action1 = "${action}Action${method}";
        $action2 = "${action}Action";

        $refl = null;
        foreach ([$action1, $action2] as $action) {
            try {
                $refl = new \ReflectionMethod($class, $action);
                if (!$refl->isPublic()) {
                    throw new NotFoundException("Controller method '$class::$action' is not a public method.");
                }

                return [$class, $action, $args];
            } catch (\ReflectionException $e) {
                ;
            }
        }

        return false;
    }



    /**
     * Call the controller action with optional arguments and call
     * initialisation methods if available.
     *
     * @param string $callable with details on what controller action to call.
     *
     * @return mixed result from the handler.
     */
    protected function handleAsControllerAction(array $callable)
    {
        $class = $callable[0];
        $action = $callable[1];
        $args = $callable[2];

        $obj = new $class();
        $refl = new \ReflectionMethod($class, "initialize");
        if ($refl->isPublic()) {
            $obj->initialize();
        }

        try {
            $res = $obj->$action(...$args);
        } catch (\ArgumentCountError $e) {
            throw new NotFoundException($e->getMessage());
        } catch (\TypeError $e) {
            throw new NotFoundException($e->getMessage());
        }

        return $res;
    }



    /**
     * Handle as callable support callables where the method is not static.
     *
     * @param string|array                 $action    base for the callable
     * @param array                        $arguments optional arguments
     * @param ContainerInjectableInterface $di        container with services
     *
     * @return mixed as the result from the route handler.
     */
    protected function handleAsCallable(
        $action,
        array $arguments
    ) {
        if (is_array($action)
            && isset($action[0])
            && isset($action[1])
            && is_string($action[0])
            && is_string($action[1])
            && class_exists($action[0])
        ) {
            // ["SomeClass", "someMethod"] but not static
            $refl = new \ReflectionMethod($action[0], $action[1]);
            if ($refl->isPublic() && !$refl->isStatic()) {
                $obj = new $action[0]();
                return $obj->{$action[1]}();
            }
        }

        return call_user_func($action, ...$arguments);
    }



    // /**
    //  * Load callable as a service from the $di container.
    //  *
    //  * @param string|array                 $action    base for the callable
    //  * @param array                        $arguments optional arguments
    //  * @param ContainerInjectableInterface $di        container with services
    //  *
    //  * @return mixed as the result from the route handler.
    //  */
    // protected function handleUsingDi(
    //     $action,
    //     array $arguments,
    //     ContainerInjectableInterface $di
    // ) {
    //     if (!$di->has($action[0])) {
    //         throw new ConfigurationException("Routehandler '{$action[0]}' not loaded in di.");
    //     }
    //
    //     $service = $di->get($action[0]);
    //     if (!is_callable([$service, $action[1]])) {
    //         throw new ConfigurationException(
    //             "Routehandler '{$action[0]}' does not have a callable method '{$action[1]}'."
    //         );
    //     }
    //
    //     return call_user_func(
    //         [$service, $action[1]],
    //         ...$arguments
    //     );
    // }
}
