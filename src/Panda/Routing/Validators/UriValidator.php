<?php

namespace Panda\Routing\Validators;

use Panda\Http\Request;
use Panda\Routing\Route;

/**
 * Class UriValidator
 *
 * @package Panda\Routing\Validators
 * @version 0.1
 */
class UriValidator implements ValidatorInterface
{

    /**
     * Validate a given rule against a route and request.
     *
     * @param Route   $route
     * @param Request $request
     *
     * @return bool
     */
    public function matches(Route $route, Request $request)
    {
        $path = $request->getBasePath() == '/' ? '/' : '/' . $request->getBasePath();

        return preg_match($route->getCompiled()->getRequirements(), rawurldecode($path));
    }
}

?>