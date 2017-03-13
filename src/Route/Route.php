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
    private $rule;           // The rule for this route
    private $action;         // The callback to handle this route
    private $arguments = []; // Arguments for the callback



    /**
     * Set values for route.
     *
     * @param string   $rule   for this route
     * @param callable $action callable to implement a controller for the route
     *
     * @return $this
     */
    public function set($rule, $action)
    {
        $this->rule = $rule;
        $this->action = $action;

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
     * Check if the route matches a query
     *
     * @param string $query to match against
     *
     * @return boolean true if query matches the route
     */
    public function match($query)
    {
        $ruleParts  = explode('/', $this->rule);
        $queryParts = explode('/', $query);
        $ruleCount = max(count($ruleParts), count($queryParts));
        $args = [];

        // If default route, match anything
        if ($this->rule == "*") {
            return true;
        }

        $match = false;
        for ($i = 0; $i < $ruleCount; $i++) {
            $rulePart  = isset($ruleParts[$i])  ? $ruleParts[$i]  : null;
            $queryPart = isset($queryParts[$i]) ? $queryParts[$i] : null;

            // Support various rules for matching the parts
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

            // Continue as long as each part matches
            if (!$match) {
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
