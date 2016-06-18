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

use Exception;
use Panda\Container\Container;
use Panda\Contracts\Bootstrapper;
use Panda\Contracts\Http\Kernel as KernelInterface;
use Panda\Foundation\Http\Kernel;
use Panda\Http\Request;

/**
 * Panda application manager.
 *
 * @package Panda\Foundation
 *
 * @version 0.1
 */
class Application extends Container implements Bootstrapper
{
    /**
     * The application's base path.
     *
     * @var string
     */
    protected $basePath;

    /**
     * The application's storage path.
     *
     * @var string
     */
    protected $storagePath;

    /**
     * Create a new panda application instance.
     *
     * @param string $basePath
     * @param string $environment
     */
    public function __construct($basePath = null, $environment = '')
    {
        // Construct container
        parent::__construct();

        // Set object properties
        if (!empty($basePath)) {
            $this->setBasePath($basePath);
        }

        // Set the application environment
        $environment = ($environment ?: getenv('APPLICATION_ENV'));
        $environment = ($environment ?: 'default');

        // Register all bindings
        $this->registerAppBindings($environment);
        $this->registerServiceBindings();
    }

    /**
     * Register application bindings.
     *
     * @param string $environment
     */
    private function registerAppBindings($environment)
    {
        static::setInstance($this);

        // Set container
        $this->set('app', $this);
        $this->set('Panda\Foundation\Application', $this);
        $this->set('Panda\Container\Container', $this);

        // Set the application environment
        $this->set('env', $environment);
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
     * Initialize the framework with the given bootstrappers.
     *
     * @param Request $request
     * @param array   $bootstrappers
     */
    public function boot($request, $bootstrappers = [])
    {
        // Boot all the bootstrappers
        foreach ($bootstrappers as $bootstrapper) {
            $this->make($bootstrapper)->boot($request, $this->get('env'));
        }

        // Bind paths, after setting up configuration
        $this->bindPathsInContainer();
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

        if (!empty($this->config('paths'))) {
            $this->bindPathsInContainer();
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config';
    }

    /**
     * @return string
     */
    public function getRoutesPath()
    {
        $paths = $this->config('paths');
        $routes = $paths['routes'];
        if (empty($routes)) {
            // Fallback to default
            return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'routes' . DIRECTORY_SEPARATOR . 'main.php';
        }

        return $this->basePath . DIRECTORY_SEPARATOR . $routes['base_dir'] . DIRECTORY_SEPARATOR . $routes['base_file'];
    }

    /**
     * @return string
     */
    public function getViewsPath()
    {
        $paths = $this->config('paths');
        $views = $paths['views'];
        if (empty($views)) {
            // Fallback to default
            return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'views';
        }

        return $this->basePath . DIRECTORY_SEPARATOR . $views['base_dir'];
    }

    /**
     * @return string
     */
    public function getAppPath()
    {
        $paths = $this->config('paths');
        $source = $paths['source'];
        if (empty($source)) {
            // Fallback to default
            return $this->basePath . DIRECTORY_SEPARATOR . 'app';
        }

        return $this->basePath . DIRECTORY_SEPARATOR . $source['base_dir'];
    }

    /**
     * @return string
     */
    public function getLangPath()
    {
        $paths = $this->config('paths');
        $lang = $paths['lang'];
        if (empty($lang)) {
            // Fallback to default
            return $this->basePath . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR . 'lang';
        }

        return $this->basePath . DIRECTORY_SEPARATOR . $lang['base_dir'];
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

    /**
     * Get a configuration value.
     *
     * @param string $name
     *
     * @return mixed
     */
    public function config($name)
    {
        // Get configuration
        try {
            $config = $this->get('config');
        } catch (Exception $ex) {
            return null;
        }

        // Get value
        return $config[$name];
    }
}