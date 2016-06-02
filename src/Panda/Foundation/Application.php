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

namespace Panda\Foundation;

use Panda\Container\Container;
use Panda\Contracts\Http\Kernel as KernelInterface;
use Panda\Foundation\Http\Kernel;
use Panda\Http\Request;

/**
 * Panda application manager.
 *
 * @package Panda\Foundation
 * @version 0.1
 */
class Application extends Container
{
    /**
     * The application's base path.
     *
     * @type string
     */
    protected $basePath;

    /**
     * The application's storage path.
     *
     * @type string
     */
    protected $storagePath;

    /**
     * Create a new panda application instance.
     *
     * @param string $basePath
     */
    public function __construct($basePath = null)
    {
        // Construct container
        parent::__construct();

        // Set object properties
        if (!empty($basePath)) {
            $this->setBasePath($basePath);
        }

        // Register all bindings
        $this->registerAppBindings();
        $this->registerServiceBindings();
    }

    /**
     * Register application bindings.
     */
    private function registerAppBindings()
    {
        static::setInstance($this);

        // Set container
        $this->set('app', $this);
        $this->set('Panda\Foundation\Application', $this);
        $this->set('Panda\Container\Container', $this);
    }

    /**
     * Register service bindings.
     */
    private function registerServiceBindings()
    {
        // Set container interfaces (manually, to be removed)
        $this->set(KernelInterface::class, \DI\object(Kernel::class));
    }

    /**
     * Initialize the framework with the given initializers.
     *
     * @param array   $initializers
     * @param Request $request
     */
    public function init($initializers, Request $request)
    {
        // Initialize all needed
        foreach ($initializers as $initializer) {
            $this->make($initializer)->init($request);
        }
    }

    /**
     * Bind all of the application paths in the container.
     */
    protected function bindPathsInContainer()
    {
        $this->set('path', $this->getAppPath());
        $this->set('path.base', $this->getBasePath());
        $this->set('path.lang', $this->getLangPath());
        $this->set('path.config', $this->getConfigPath());
        $this->set('path.public', $this->getPublicPath());
        $this->set('path.storage', $this->getStoragePath());
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     *
     * @return Application
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;

        $this->bindPathsInContainer();

        return $this;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . "config";
    }

    /**
     * @return string
     */
    public function getRoutesPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "routes" . DIRECTORY_SEPARATOR . "main.php";
    }

    /**
     * @return string
     */
    public function getViewsPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . "resources" . DIRECTORY_SEPARATOR . "views";
    }

    /**
     * @return string
     */
    public function getAppPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'app';
    }

    /**
     * @return string
     */
    public function getLangPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'lang';
    }

    /**
     * Get the path to the public / web directory.
     *
     * @return string
     */
    public function getPublicPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'public';
    }

    /**
     * Get the path to the storage directory.
     *
     * @return string
     */
    public function getStoragePath()
    {
        return $this->storagePath ?: $this->basePath . DIRECTORY_SEPARATOR . 'storage';
    }

    /**
     * @param string $storagePath
     *
     * @return Application
     */
    public function setStoragePath($storagePath)
    {
        $this->storagePath = $storagePath;

        $this->bindPathsInContainer();

        return $this;
    }
}

?>