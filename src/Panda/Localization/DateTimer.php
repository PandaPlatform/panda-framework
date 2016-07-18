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
 * Class DateTimer
 *
 * @package Panda\Localization
 *
 * @version 0.1
 */
class DateTimer implements Bootstrapper
{
    /**
     * The default timezone for the framework.
     */
    const DEFAULT_TIMEZONE = 'GMT';

    /**
     * @var string
     */
    protected $defaultTimeZone = null;

    /**
     * Init session.
     *
     * @param Request $request
     */
    public function boot($request)
    {
        try {
            // Try to get timezone by ip
            $geoIp = new GeoIp($request);
            $timezone = $geoIp->getTimezoneByIP();
        } catch (Exception $ex) {
            $timezone = $this->getDefaultTimeZone();
        }

        $this->set($timezone);
    }

    /**
     * Sets the current timezone for the system and for the user.
     *
     * @param string $timezone
     */
    public function set($timezone)
    {
        // Set php timezone
        date_default_timezone_set($timezone);
    }

    /**
     * Get the current timezone.
     *
     * @return string The current timezone.
     */
    public function get()
    {
        return date_default_timezone_get();
    }

    /**
     * @return string
     */
    public function getDefaultTimeZone()
    {
        return $this->defaultTimeZone ?: static::DEFAULT_TIMEZONE;
    }

    /**
     * @param string $defaultTimeZone
     */
    public function setDefaultTimeZone($defaultTimeZone)
    {
        $this->defaultTimeZone = $defaultTimeZone;
    }
}
