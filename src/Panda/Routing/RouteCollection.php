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
     * Add a Route instance to the collection.
     *
     * @param Route $route
     *
     * @return Route
     */
    public function add(Route $route)
    {
        //$this->addToCollections($route);
        //$this->addLookups($route);
        return $route;
    }

    /**
     * Find the first route matching a given request.
     *
     */
    public function match(Request $request)
    {
        $routes = $this->get($request->getMethod());
        // First, we will see if we can find a matching route for this current request
        // method. If we can, great, we can just return it so that it can be called
        // by the consumer. Otherwise we will check for routes with another verb.
        $route = $this->check($routes, $request);
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
     * Get all of the routes in the collection.
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
}

?>