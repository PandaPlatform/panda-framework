<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Foundation\Http;

use InvalidArgumentException;
use Panda\Contracts\Http\Kernel as KernelInterface;
use Panda\Foundation\Application;
use Panda\Http\Request;
use Panda\Http\Response;
use Panda\Routing\Router;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Panda kernel
 *
 * @package Panda\Foundation\Http
 *
 * @version 0.1
 */
class Kernel implements KernelInterface
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var string[]
     */
    protected $bootstrappers = [
        '\Panda\Foundation\Bootstrap\Environment',
        '\Panda\Foundation\Bootstrap\Configuration',
        '\Panda\Foundation\Bootstrap\Logging',
        '\Panda\Foundation\Bootstrap\FacadeRegistry',
    ];

    /**
     * Kernel constructor.
     *
     * @param Application $app
     * @param Router      $router
     */
    public function __construct(Application $app, Router $router)
    {
        $this->app = $app;
        $this->router = $router;
    }

    /**
     * Init the panda application and start all the interfaces that
     * are needed for runtime.
     *
     * @param Request|SymfonyRequest $request
     */
    public function boot($request)
    {
        // Initialize application
        $this->app->boot($request, $this->bootstrappers);

        // Set bindings
        $this->app->set('Kernel', $this);

        // Include routes
        include_once $this->app->getRoutesPath();
    }

    /**
     * Handle the incoming request and return a response.
     *
     * @param Request|SymfonyRequest $request
     *
     * @return Response
     */
    public function handle(SymfonyRequest $request)
    {
        // Boot kernel
        $this->boot($request);

        // Dispatch the response
        return $this->router->dispatch($request);
    }

    /**
     * Terminate the kernel process finalizing response information.
     *
     * @param Request|SymfonyRequest   $request
     * @param Response|SymfonyResponse $response
     */
    public function terminate(SymfonyRequest $request, SymfonyResponse $response)
    {

    }

    /**
     * Add a bootstrapper to the application flow.
     *
     * @param string $bootstrapper
     *
     * @throws InvalidArgumentException
     */
    public function addExternalBootstrapper($bootstrapper)
    {
        // Add to the queue
        $this->bootstrappers[] = $bootstrapper;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return Request
     */
    public function getCurrentRequest()
    {
        return $this->router->getCurrentRequest();
    }
}
