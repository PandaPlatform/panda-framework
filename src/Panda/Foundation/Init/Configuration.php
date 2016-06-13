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

namespace Panda\Foundation\Init;

use Panda\Contracts\Init\Initializer;
use Panda\Foundation\Application;
use Panda\Http\Request;

/**
 * Class Configuration
 * Initializes configuration variables.
 *
 * @package Panda\Foundation\Init
 * @version 0.1
 */
class Configuration implements Initializer
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
     */
    public function init($request)
    {
        // Load configuration file
        $configFile = $this->app->getConfigPath() . DIRECTORY_SEPARATOR . "config.json";
        if (file_exists($configFile)) {
            // Load and decode json config
            $config = file_get_contents($configFile);
            $configArray = json_decode($config, true);

            // If valid, set application config
            if (!empty($configArray)) {
                $this->app->set('config', $configArray);
            }
        }
    }
}