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
                    $match = false;
                    if (substr($rulePart, -1) == "}"
                        && !is_null($queryPart)
                    ) {
                        $args[] = $queryPart;
                        $match = true;
                    }
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
