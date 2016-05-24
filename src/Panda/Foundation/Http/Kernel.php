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
use Panda\Contracts\Init\Initializer;
use Panda\Foundation\Application;
use Panda\Http\Request;
use Panda\Http\Response;
use Panda\Routing\Router;

/**
 * Panda kernel
 *
 * @package Panda\Foundation\Http
 * @version 0.1
 */
class Kernel
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
        'Panda\Foundation\Session'
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
     * Handle the incoming request and return a response.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request)
    {
        // Initialize application
        $this->init($request);

        return $this->router->dispatch($request);
    }

    /**
     * Terminate the kernel process finalizing response information.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response)
    {

    }

    /**
     * Init the panda application and start all the interfaces that
     * are needed for runtime.
     *
     * @param Request $request
     */
    private function init(Request $request)
    {
        // Initialize all needed
        foreach ($this->initializers as $initializer) {
            $initializer->init($request);
        }
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
}