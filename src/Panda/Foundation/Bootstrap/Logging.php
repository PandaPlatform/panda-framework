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

namespace Panda\Foundation\Bootstrap;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\WebProcessor;
use Panda\Contracts\Bootstrapper;
use Panda\Foundation\Application;
use Panda\Http\Request;
use Panda\Log\Logger;
use Psr\Log\LoggerInterface;

/**
 * Class Configuration
 * Initializes configuration variables.
 *
 * @package Panda\Foundation\Bootstrap
 *
 * @version 0.1
 */
class Logging implements Bootstrapper
{
    /**
     * @var Application
     */
    private $app;

    /**
     * Environment constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Boot the bootstrapper.
     *
     * @param Request $request
     */
    public function boot($request)
    {
        // Create the logger
        $logger = new Logger('application_logger');

        // Add error handler
        $path = $this->app->config('logger.base_dir') . '/' . $this->app->config('logger.error');
        $maxFilesCount = $this->app->config('logger.max_files_count');
        $logger->pushHandler(new RotatingFileHandler($path, $maxFilesCount, Logger::ERROR));

        // Add debug handler
        $path = $this->app->config('logger.base_dir') . '/' . $this->app->config('logger.debug');
        $maxFilesCount = $this->app->config('logger.max_files_count');
        $logger->pushHandler(new RotatingFileHandler($path, $maxFilesCount, Logger::DEBUG));

        // Push other processors
        $logger->pushProcessor(new PsrLogMessageProcessor());
        $logger->pushProcessor(new IntrospectionProcessor());
        $logger->pushProcessor(new WebProcessor());

        // Set application logger
        $this->setBindings($logger);
    }

    /**
     * Set logging bindings.
     *
     * @param LoggerInterface $logger
     */
    private function setBindings($logger)
    {
        $this->app->set(LoggerInterface::class, $logger);
    }
}