<?php

namespace Anax\Route;

/**
 * A container for routes.
 *
 */
class Route
{

    /**
    * Properties
    *
    */
    private $name;           // A name for this route
    private $method;         // The methods to support
    private $rule;           // The rule for this route
    private $action;         // The callback to handle this route
    private $arguments = []; // Arguments for the callback



    /**
     * Set values for route.
     *
     * @param null|string       $rule   for this route
     * @param callable          $action callable to implement a controller for
     *                                  the route
     * @param null|string|array $method as request method to support
     *
     * @return $this
     */
    public function set($rule, $action = null, $method = null)
    {
        $this->rule = $rule;
        $this->action = $action;

        $this->method = $method;
        if (is_string($method)) {
            $this->method = array_map("trim", explode("|", $method));
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
            if ($pos !== false) {
                $type = substr($part, $pos + 1);
                if (! $this->checkPartMatchingType($queryPart, $type)) {
                    return false;
                }
            }
            $args[] = $queryPart;
            return true;
        }
        return false;
    }



    /**
     * Check if value is matching a certain type of values.
     *
     * @param string $rulePart   the rule part to check.
     * @param string $queryPart  the query part to check.
     * @param array  &$args      add argument to args array if matched
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
            || $this->rule == "*"
            || $this->rule == "**"
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
     * @return void
     */
    public function handle()
    {
        return call_user_func($this->action, ...$this->arguments);
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
     * Get the rule for the route.
     *
     * @return string
     */
    public function getRule()
    {
        return $this->rule;
    }
}
