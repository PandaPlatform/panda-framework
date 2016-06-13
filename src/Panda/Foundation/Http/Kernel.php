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

namespace Panda\Foundation\Http;

use InvalidArgumentException;
use Panda\Contracts\Http\Kernel as KernelInterface;
use Panda\Contracts\Init\Initializer;
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
     * @var Initializer[]
     */
    protected $initializers = [
        '\Panda\Foundation\Init\Environment',
        '\Panda\Foundation\Init\FacadeRegistry',
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
    public function init(SymfonyRequest $request)
    {
        // Initialize application
        $this->app->init($this->initializers, $request);

        // Set bindings
        $this->app->set('kernel', $this);

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
        // Initialize kernel
        $this->init($request);

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
     * Add an initializer to the application flow.
     *
     * @param string $initializer
     *
     * @throws InvalidArgumentException
     */
    public function addExternalInitializer($initializer)
    {
        // Add to the queue
        $this->initializers[] = $initializer;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        return $this->app;
    }

    /**
     * @return SymfonyRequest
     */
    public function getCurrentRequest()
    {
        return $this->router->getCurrentRequest();
    }
}