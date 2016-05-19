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

/**
 * Application foundation manager.
 *
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

        // Make the container lightweight
        $this->useAutowiring(false);
        $this->useAnnotations(false);
        $this->ignorePhpDocErrors(true);

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
     * @throws \DI\NotFoundException
     */
    public function get($name)
    {
        return $this->containerHandler->get($name);
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