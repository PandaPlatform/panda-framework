<?php

namespace Panda\Routing\Validators;

use Panda\Http\Request;
use Panda\Routing\Route;

/**
 * Class ΗοστValidator
 *
 * @package Panda\Routing\Validators
 * @version 0.1
 */
class HostValidator implements ValidatorInterface
{
    /**
     * Validate a given rule against a route and request.
     *
     * @param  Route   $route
     * @param  Request $request
     *
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        if (is_null($route->getCompiled()->getHostRegex())) {
            return true;
        }

        return preg_match($route->getCompiled()->getHostRegex(), $request->getHost());
    }
}

?>