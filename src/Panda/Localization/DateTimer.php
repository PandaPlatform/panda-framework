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

namespace Panda\Localization;

use Exception;
use Panda\Contracts\Init\Initializer;
use Panda\Http\Request;

/**
 * Class DateTimer
 *
 * @package Panda\Localization
 * @version 0.1
 */
class DateTimer implements Initializer
{
    /**
     * The default timezone for the framework.
     */
    const DEFAULT_TIMEZONE = 'GMT';

    /**
     * Init session.
     *
     * @param Request $request
     */
    public function init($request)
    {
        try {
            // Try to get timezone by ip
            $geoIp = new GeoIp($request);
            $timezone = $geoIp->getTimezoneByIP();
        } catch (Exception $ex) {
            $timezone = static::DEFAULT_TIMEZONE;
        } finally {
            $this->set($timezone);
        }
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
}