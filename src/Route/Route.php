<?php

namespace Anax\Route;

use \Anax\Route\Exception\ConfigurationException;

/**
 * A container for routes.
 *
 */
class Route
{
    /**
     * @var string       $name      a name for this route.
     * @var string       $info      description of route.
     * @var string|array $method    the method(s) to support
     * @var string       $rule      the path rule for this route
     * @var callable     $action    the callback to handle this route
     * @var null|array   $arguments arguments for the callback, extracted
     *                              from path
     */
    private $name;
    private $info;
    private $method;
    private $rule;
    private $action;
    private $arguments = [];



    /**
     * Set values for route.
     *
     * @param null|string       $rule   for this route
     * @param callable          $action callable to implement a controller for
     *                                  the route
     * @param null|string|array $method as request method to support
     * @param null|string       $info   description of the route
     *
     * @return $this
     */
    public function set($rule, $action = null, $method = null, $info = null)
    {
        $this->rule = $rule;
        $this->action = $action;
        $this->info = $info;

        $this->method = $method;
        if (is_string($method)) {
            $this->method = array_map("trim", explode("|", $method));
        }
        if (is_array($this->method)) {
            $this->method = array_map("strtoupper", $this->method);
        }

        return $this;
    }



    /**
     * Check if part of route is a argument and optionally match type
     * as a requirement {argument:type}.
     *
     * @param string $rulePart   the rule part to check.
     * @param string $queryPart  the query part to check.
     * @param array  &$args      add argument to args array if matched
     *
     * @return boolean
     */
    private function checkPartAsArgument($rulePart, $queryPart, &$args)
    {
        if (substr($rulePart, -1) == "}"
            && !is_null($queryPart)
        ) {
            $part = substr($rulePart, 1, -1);
            $pos = strpos($part, ":");
            $type = null;
            if ($pos !== false) {
                $type = substr($part, $pos + 1);
                if (! $this->checkPartMatchingType($queryPart, $type)) {
                    return false;
                }
            }
            $args[] = $this->typeConvertArgument($queryPart, $type);
            return true;
        }
        return false;
    }



    /**
     * Check if value is matching a certain type of values.
     *
     * @param string $value   the value to check.
     * @param array  $type    the expected type to check against.
     *
     * @return boolean
     */
    private function checkPartMatchingType($value, $type)
    {
        switch ($type) {
            case "digit":
                return ctype_digit($value);
                break;

            case "hex":
                return ctype_xdigit($value);
                break;

            case "alpha":
                return ctype_alpha($value);
                break;

            case "alphanum":
                return ctype_alnum($value);
                break;

            default:
                return false;
        }
    }



    /**
     * Check if value is matching a certain type and do type
     * conversion accordingly.
     *
     * @param string $value   the value to check.
     * @param array  $type    the expected type to check against.
     *
     * @return boolean
     */
    private function typeConvertArgument($value, $type)
    {
        switch ($type) {
            case "digit":
                return (int) $value;
                break;

            default:
                return $value;
        }
    }



    /**
     * Match part of rule and query.
     *
     * @param string $rulePart   the rule part to check.
     * @param string $queryPart  the query part to check.
     * @param array  &$args      add argument to args array if matched
     *
     * @return boolean
     */
    private function matchPart($rulePart, $queryPart, &$args)
    {
        $match = false;
        $first = isset($rulePart[0]) ? $rulePart[0] : '';
        switch ($first) {
            case '*':
                $match = true;
                break;

            case '{':
                $match = $this->checkPartAsArgument($rulePart, $queryPart, $args);
                break;

            default:
                $match = ($rulePart == $queryPart);
                break;
        }
        return $match;
    }



    /**
     * Check if the request method matches.
     *
     * @param string $method as request method
     *
     * @return boolean true if request method matches
     */
    public function matchRequestMethod($method)
    {
        if ($method && is_array($this->method) && !in_array($method, $this->method)
            || (is_null($method) && !empty($this->method))
        ) {
            return false;
        }
        return true;
    }



    /**
     * Check if the route matches a query and request method.
     *
     * @param string $query  to match against
     * @param string $method as request method
     *
     * @return boolean true if query matches the route
     */
    public function match($query, $method = null)
    {
        if (!$this->matchRequestMethod($method)) {
            return false;
        }

        // If any/default */** route, match anything
        if (is_null($this->rule)
            || in_array($this->rule, ["*", "**"])
        ) {
            return true;
        }

        // Check all parts to see if they matches
        $ruleParts  = explode('/', $this->rule);
        $queryParts = explode('/', $query);
        $ruleCount = max(count($ruleParts), count($queryParts));
        $args = [];

        for ($i = 0; $i < $ruleCount; $i++) {
            $rulePart  = isset($ruleParts[$i])  ? $ruleParts[$i]  : null;
            $queryPart = isset($queryParts[$i]) ? $queryParts[$i] : null;

            if ($rulePart === "**") {
                break;
            }

            if (!$this->matchPart($rulePart, $queryPart, $args)) {
                return false;
            }
        }

        $this->arguments = $args;
        return true;
    }



    /**
     * Handle the action for the route.
     *
     * @param string $di container with services
     *
     * @return mixed
     */
    public function handle($di = null)
    {
        if (is_callable($this->action)) {
            return call_user_func($this->action, ...$this->arguments);
        }

        // Try to load service from app/di injected container
        if ($di
            && is_array($this->action)
            && isset($this->action[0])
            && isset($this->action[1])
            && is_string($this->action[0])
        ) {
            if (!$di->has($this->action[0])) {
                throw new ConfigurationException("Routehandler '{$this->action[0]}' not loaded in di.");
            }

            $service = $di->get($this->action[0]);
            if (!is_callable([$service, $this->action[1]])) {
                throw new ConfigurationException(
                    "Routehandler '{$this->action[0]}' does not have a callable method '{$this->action[1]}'."
                );
            }

            return call_user_func(
                [$service, $this->action[1]],
                ...$this->arguments
            );
        }

        if (!is_null($this->action)) {
            throw new ConfigurationException("Routehandler '{$this->rule}' does not have a callable action.");
        }
    }



    /**
     * Set the name of the route.
     *
     * @param string $name set a name for the route
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }



    /**
     * Get information of the route.
     *
     * @return null|string as route information.
     */
    public function getInfo()
    {
        return $this->info;
    }



    /**
     * Get the rule for the route.
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }



    /**
     * Get the request method for the route.
     *
     * @return string representing the request method supported
     */
    public function getRequestMethod()
    {
        if (is_array($this->method)) {
            return implode("|", $this->method);
        }

        return $this->method;
    }
}
