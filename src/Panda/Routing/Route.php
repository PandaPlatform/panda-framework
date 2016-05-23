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
use HttpResponseException;
use Panda\Http\Request;
use Panda\Routing\Validators\MethodValidator;
use Panda\Routing\Validators\UriValidator;
use Symfony\Component\Routing\Route as SymfonyRoute;

/**
 * Class Route
 *
 * @package Panda\Routing
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
     * @type SymfonyRoute
     */
    private $compiled;

    /**
     * @type array
     */
    private static $validators;

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
        // Create compiled route
        $this->compileRoute();

        foreach ($this->getValidators() as $validator) {
            // Check for method validator explicitly
            if (!$includingMethod && $validator instanceof MethodValidator) {
                continue;
            }

            // Check if validator matches
            if (!$validator->matches($this, $request)) {
                return false;
            }
        }

        // All match
        return true;
    }

    /**
     * Compile the current route.
     */
    protected function compileRoute()
    {
        $uri = preg_replace('/\{(\w+?)\?\}/', '{$1}', $this->uri);

        $this->compiled = (new SymfonyRoute($uri, $optionals = array(), $requirements = array(), [], $domain = ''))->compile();
    }

    /**
     * Get the route validators for the instance.
     *
     * @return array
     */
    public static function getValidators()
    {
        if (isset(static::$validators)) {
            return static::$validators;
        }

        // Set a series of validators to validate whether a route matches some criteria.
        return static::$validators = [
            new MethodValidator,
            // new SchemeValidator,
            // new HostValidator,
            new UriValidator
        ];
    }

    /**
     * Run the route action and return the response.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function run(Request $request)
    {
        try {
            // Run route's callable action
            return $this->callback->call($this, $request);
        } catch (HttpResponseException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return SymfonyRoute
     */
    public function getCompiled()
    {
        return $this->compiled;
    }
}

?>