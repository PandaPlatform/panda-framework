<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Localization;

use Exception;
use Panda\Contracts\Bootstrapper;
use Panda\Http\Request;

/**
 * Class Locale
 *
 * @package Panda\Localization
 *
 * @version 0.1
 */
class Locale
{
    /**
     * @var string
     */
    protected static $locale = null;

    /**
     * @var string
     */
    protected static $defaultLocale = null;

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public static function get()
    {
        return static::$locale;
    }

    /**
     * Set the current application locale.
     *
     * @param string $locale
     */
    public static function set($locale)
    {
        static::$locale = $locale;
    }

    /**
     * Get the default application locale.
     *
     * @return string
     */
    public static function getDefault()
    {
        return static::$defaultLocale;
    }

    /**
     * Set the default application locale.
     *
     * @param string $defaultLocale
     */
    public static function setDefault($defaultLocale)
    {
        static::$defaultLocale = $defaultLocale;
    }
}