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
use Panda\Support\Facades\Facade;

/**
 * Panda kernel
 *
 * @package Panda\Foundation\Http
 * @version 0.1
 */
class Kernel implements KernelInterface
{
    /**
     * @type Application
     */
    protected $app;

    /**
     * @type Router
     */
    protected $router;

    /**
     * @type Initializer[]
     */
    protected $initializers = [
        '\Panda\Debug\Debugger',
        '\Panda\Session\Session',
        '\Panda\Localization\DateTimer',
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
     * @param Request $request
     */
    public function init($request)
    {
        // Initialize all needed
        foreach ($this->initializers as $initializer) {
            $this->app->get($initializer)->init($request);
        }

        // Include routes
        include_once $this->app->getRoutesPath();
    }

    /**
     * Handle the incoming request and return a response.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle($request)
    {
        // Set facade application container
        Facade::setFacadeApp($this->app);

        // Initialize application
        $this->init($request);

        // Dispatch the response
        return $this->router->dispatch($request);
    }

    /**
     * Terminate the kernel process finalizing response information.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function terminate($request, $response)
    {

    }

    /**
     * Add an initializer to the application flow.
     *
     * @param Initializer $initializer
     *
     * @throws InvalidArgumentException
     */
    public function addExternalInitializer(Initializer $initializer)
    {
        if (empty($initializer) || !($initializer instanceof Initializer)) {
            throw new InvalidArgumentException("The given parameter is not a valid initializer.");
        }

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
}