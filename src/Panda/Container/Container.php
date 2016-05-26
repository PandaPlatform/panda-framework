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

namespace Panda\Container;

use DI\Container as DIContainer;
use DI\ContainerBuilder;
use DI\Definition\Helper\DefinitionHelper;
use DI\NotFoundException;

/**
 * Application foundation manager.
 *
 * @package Panda\Container
 * @version 0.1
 */
class Container extends ContainerBuilder
{
    /**
     * @type DIContainer
     */
    private $containerHandler;

    /**
     * Container constructor.
     */
    public function __construct()
    {
        // Create the Container Builder
        parent::__construct($containerClass = 'DI\Container');

        $this->containerHandler = $this->build();
    }

    /**
     * Define an object or a value in the container.
     *
     * @param string                 $name       Entry name
     * @param mixed|DefinitionHelper $definition Value, use definition helpers to define objects
     */
    public function set($name, $definition)
    {
        $this->containerHandler->set($name, $definition);
    }

    /**
     * Get an entry of the container by its name.
     *
     * @param string $name Entry name or a class name.
     *
     * @return mixed
     * @throws NotFoundException
     */
    public function get($name)
    {
        return $this->containerHandler->get($name);
    }

    /**
     * Build an entry of the container by its name.
     *
     * @param string $name
     * @param array  $parameters
     *
     * @return mixed
     * @throws NotFoundException
     */
    public function make($name, $parameters = array())
    {
        return $this->containerHandler->make($name, $parameters);
    }

    /**
     * Test if the container can provide something for the given name.
     *
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return $this->containerHandler->has($name);
    }

    /**
     * Call the given function using the given parameters.
     *
     * @param callable $name
     * @param array    $parameters
     *
     * @return bool
     */
    public function call($name, $parameters)
    {
        return $this->containerHandler->call($name, $parameters);
    }

    /**
     * @return DIContainer
     */
    public function getContainerHandler()
    {
        return $this->containerHandler;
    }
}

?>