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

namespace Panda\Support\Configuration;

use Panda\Contracts\Configuration\ConfigurationHandler;
use Panda\Support\Helpers\ArrayHelper;

/**
 * Class Config
 *
 * @package Panda\Support\Configuration
 *
 * @version 0.1
 */
class Config implements ConfigurationHandler
{
    /**
     * @var array
     */
    protected $config;

    /**
     * Config constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        $this->config = $config;
    }

    /**
     * Get a configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return ArrayHelper::get($this->config, $key, $default, $useDotSyntax = true);
    }
}