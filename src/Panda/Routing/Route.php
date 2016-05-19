<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Panda\Routing;

use Closure;
use Panda\Http\Request;

/**
 * Filesystem handler
 *
 * Creates, edits and deletes files.
 *
 * @version    0.1
 */
class Route
{
    /**
     * @type array
     */
    private $methods;

    /**
     * @type string
     */
    private $uri;

    /**
     * @type Closure
     */
    private $callback;

    /**
     * Create a new Route instance
     *
     * @param array   $methods  An array of all the methods that this route should listen to.
     * @param string  $uri      The uri to match for this route to execute the callback.
     * @param Closure $callback A callback function to be executed when this route is matched by the request.
     */
    public function __construct($methods, $uri, Closure $callback)
    {
        // Initialize Route properties
        $this->methods = (array)$methods;
        $this->uri = $uri;
        $this->callback = $callback;
    }

    /**
     * Determine if the route matches given request.
     *
     * @param Request $request
     * @param bool    $includingMethod
     *
     * @return bool
     */
    public function matches(Request $request, $includingMethod = true)
    {
        /*
        $this->compileRoute();
        foreach ($this->getValidators() as $validator) {
            if (!$includingMethod && $validator instanceof MethodValidator) {
                continue;
            }
            if (!$validator->matches($this, $request)) {
                return false;
            }
        }*/

        return true;
    }

    /**
     * Run the route action and return the response.
     *
     * @return mixed
     */
    protected function runCallable(Request $request)
    {
        $parameters = $this->resolveMethodDependencies(
            $this->parametersWithoutNulls(), new ReflectionFunction($this->action['uses'])
        );

        return call_user_func_array($this->action['uses'], $parameters);
    }

    /**
     * Run the route action and return the response.
     *
     * @return mixed
     */
    public function run(Request $request)
    {
        $this->container = $this->container ?: new Container;
        try {
            if (!is_string($this->action['uses'])) {
                return $this->runCallable($request);
            }

            return $this->runController($request);
        } catch (HttpResponseException $e) {
            return $e->getResponse();
        }
    }
}

?>