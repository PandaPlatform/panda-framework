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

/**
 * Panda application manager.
 *
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

        // Register application
        $this->registerBindings();
    }

    /**
     * Register application bindings.
     */
    private function registerBindings()
    {
        // Load config from .json file
        $config = array();

        // Add container definitions
        //$this->addDefinitions($config);
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
     */
    public function setBasePath($basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return string
     */
    public function getConfigPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . "config";
    }
}

?>