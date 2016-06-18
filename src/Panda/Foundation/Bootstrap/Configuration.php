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

use Panda\Contracts\Bootstrapper;
use Panda\Foundation\Application;
use Panda\Http\Request;

/**
 * Class Configuration
 * Initializes configuration variables.
 *
 * @package Panda\Foundation\Bootstrap
 *
 * @version 0.1
 */
class Configuration implements Bootstrapper
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
     * Run the initializer.
     *
     * @param Request $request
     * @param string  $environment
     */
    public function boot($request, $environment = 'default')
    {
        // Get configuration file path
        $configFile = $this->getConfigFile($environment);
        if (empty($configFile)) {
            return;
        }

        // Load configuration and set to application
        $configArray = json_decode(file_get_contents($configFile), true);
        if (!empty($configArray)) {
            $this->app->set('config', $configArray);
        }
    }

    /**
     * Get the configuration file according to the current environment.
     *
     * @param string $environment
     *
     * @return string|null
     */
    private function getConfigFile($environment = 'default')
    {
        $configFile = $this->app->getConfigPath() . DIRECTORY_SEPARATOR . 'config-' . $environment . '.json';
        if (!file_exists($configFile) && $environment != 'default') {
            $configFile = $this->getConfigFile('default');
        }
        if (!file_exists($configFile)) {
            $configFile = null;
        }

        return $configFile;
    }
}