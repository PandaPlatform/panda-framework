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
use Panda\Container\Container;
use Panda\Http\Request;
use Panda\Http\Response;

/**
 * Application router.
 * Initiates all routers from the route folder.
 *
 * @package Panda\Routing
 * @version 0.1
 */
class Router
{
    /**
     * @type Container
     */
    protected $container;

    /**
     * @type RouteCollection
     */
    protected $routes;

    /**
     * All of the verbs supported by the router.
     *
     * @var array
     */
    public static $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];

    /**
     * Create a new Router instance.
     *
     * @param Container $container
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
        $this->routes = new RouteCollection();
    }

    /**
     * Register a new GET route with the router.
     *
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    public function get($uri, $action = null)
    {
        return $this->addRoute(['GET', 'HEAD'], $uri, $action);
    }

    /**
     * Register a new POST route with the router.
     *
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    public function post($uri, $action = null)
    {
        return $this->addRoute('POST', $uri, $action);
    }

    /**
     * Register a new PUT route with the router.
     *
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    public function put($uri, $action = null)
    {
        return $this->addRoute('PUT', $uri, $action);
    }

    /**
     * Register a new PATCH route with the router.
     *
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    public function patch($uri, $action = null)
    {
        return $this->addRoute('PATCH', $uri, $action);
    }

    /**
     * Register a new DELETE route with the router.
     *
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    public function delete($uri, $action = null)
    {
        return $this->addRoute('DELETE', $uri, $action);
    }

    /**
     * Register a new OPTIONS route with the router.
     *
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    public function options($uri, $action = null)
    {
        return $this->addRoute('OPTIONS', $uri, $action);
    }

    /**
     * Register a new route responding to all verbs.
     *
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    public function all($uri, $action = null)
    {
        $verbs = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE'];

        return $this->addRoute($verbs, $uri, $action);
    }

    /**
     * Register a new route with the given verbs.
     *
     * @param  array                     $methods
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    public function any($methods, $uri, $action = null)
    {
        return $this->addRoute(array_map('strtoupper', (array)$methods), $uri, $action);
    }

    /**
     * Add a route to the underlying route collection.
     *
     * @param  array|string              $methods
     * @param  string                    $uri
     * @param  Closure|array|string|null $action
     *
     * @return Route
     */
    protected function addRoute($methods, $uri, $action)
    {
        return $this->routes->add($this->createRoute($methods, $uri, $action));
    }

    /**
     * Create a new route instance.
     *
     * @param  array|string $methods
     * @param  string       $uri
     * @param  mixed        $action
     *
     * @return Route
     */
    protected function createRoute($methods, $uri, $action)
    {
        // If the route is routing to a controller we will parse the route action into
        // an acceptable array format before registering it and creating this route
        // instance itself. We need to build the Closure that will call this out.
        /*if ($this->validateAction($action)) {
            $action = $this->convertToControllerAction($action);
        }*/
        $route = $this->newRoute(
            $methods, $uri, $action
        );
        // If we have groups that need to be merged, we will merge them now after this
        // route has already been created and is ready to go. After we're done with
        // the merge we will be ready to return the route back out to the caller.
        /*if ($this->hasGroupStack()) {
            $this->mergeGroupAttributesIntoRoute($route);
        }
        $this->addWhereClausesToRoute($route);
*/

        return $route;
    }

    /**
     * Create a new Route object.
     *
     * @param  array|string $methods
     * @param  string       $uri
     * @param  mixed        $action
     *
     * @return Route
     */
    protected function newRoute($methods, $uri, $action)
    {
        return new Route($methods, $uri, $action);
    }

    /**
     * Dispatch the request to the application.
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function dispatch(Request $request)
    {
        //$this->currentRequest = $request;

        // Get response from route and return to handler
        return $this->dispatchToRoute($request);
    }

    /**
     * Dispatch the request to a route and return the response.
     *
     * @param  Request $request
     *
     * @return mixed
     */
    public function dispatchToRoute(Request $request)
    {
        // Find the route that matches the given request
        $route = $this->getMatchingRoute($request);

        // Get response from the route
        $response = $this->runRoute($route, $request);

        return $this->prepareResponse($request, $response);
    }

    /**
     * Run the given route within a Stack "onion" instance.
     *
     * @param  Route   $route
     * @param  Request $request
     *
     * @return mixed
     */
    protected function runRoute(Route $route, Request $request)
    {
        // Run the route with the given request
        return $route->run($request);
    }

    /**
     * Find the route matching a given request.
     *
     * @param  Request $request
     *
     * @return Route
     */
    protected function getMatchingRoute($request)
    {
        // Get matching route
        $route = $this->routes->match($request);

        // Create a new instance of the route
        $this->container->getContainerHandler()->make('Panda\Routing\Route', $route);

        return $route;
        //return $this->substituteBindings($route);
    }

    /**
     * Get all the routes that match to the given request.
     */
    protected function gatherRoutes()
    {
        // Get the base route path
        $basePath = $this->container->get("app.base_path");
        $routesPath = $this->container->get("app.routes_path");

        // Include the route file
        include $basePath . DIRECTORY_SEPARATOR . $routesPath;
    }
}

?>