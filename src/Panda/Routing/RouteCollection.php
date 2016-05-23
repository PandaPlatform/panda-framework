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

use Panda\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Filesystem handler
 *
 * Creates, edits and deletes files.
 *
 * @version    0.1
 */
class RouteCollection
{
    /**
     * @type Route[]
     */
    protected $routes;

    /**
     * @type Route[]
     */
    protected $allRoutes;

    /**
     * Add a Route instance to the collection.
     *
     * @param Route $route
     *
     * @return Route
     */
    public function add(Route $route)
    {
        // Add route to collection
        $this->addRoute($route);

        //$this->addLookups($route);
        return $route;
    }

    /**
     * Find the first route matching a given request.
     *
     * @param Request $request
     *
     * @return mixed
     * @throws NotFoundHttpException
     */
    public function match(Request $request)
    {
        // Get all the routes
        $routes = $this->getByMethod($request->getMethod());

        // Get the matching route out of the collection.
        $route = $this->getMatchingRoute($routes, $request);
        if (!is_null($route)) {
            return $route->bind($request);
        }
        // If no route was found we will now check if a matching route is specified by
        // another HTTP verb. If it is we will need to throw a MethodNotAllowed and
        // inform the user agent of which HTTP verb it should use for this route.
        $others = $this->checkForAlternateVerbs($request);
        if (count($others) > 0) {
            return $this->getRouteForMethods($request, $others);
        }

        throw new NotFoundHttpException;
    }

    /**
     * Check which of the given routes matches the given request.
     *
     * @param Route[] $routes
     * @param Request $request
     *
     * @return null|Route
     */
    public function getMatchingRoute($routes, Request $request)
    {
        foreach ($routes as $matchingRoute) {
            if ($matchingRoute->matches($request)) {
                return $matchingRoute;
            }
        }

        return null;
    }

    /**
     * Get all matched routes according to the given request method.
     *
     * @param  string|null $method
     *
     * @return array
     */
    public function getByMethod($method = null)
    {
        if (is_null($method)) {
            return $this->getRoutes();
        }

        // Filter routes by the given method
        return Arr::get($this->routes, $method, []);
    }

    /**
     * Get all of the routes in the collection.
     *
     * @return array
     */
    public function getRoutes()
    {
        return array_values($this->allRoutes);
    }

    /**
     * Add the given route to the collection.
     *
     * @param Route $route
     */
    protected function addRoute($route)
    {
        $this->routes[] = $route;
    }
}

?>