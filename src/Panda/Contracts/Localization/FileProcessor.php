<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Contracts\Localization;

/**
 * Interface FileProcessor
 *
 * @package Panda\Contracts\Localization
 *
 * @version 0.1
 */
interface FileProcessor
{
    /**
     * Get a translation value.
     *
     * @param string $key
     * @param string $locale
     * @param string $package
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $locale, $package = 'default', $default = null);

    /**
     * Set the base literals directory.
     *
     * @param string $directory
     *
     * @return $this
     */
    public function setBaseDirectory($directory);
}
