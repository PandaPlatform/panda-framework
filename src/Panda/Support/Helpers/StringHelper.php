<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Panda\Support\Helpers;

/**
 * Class StringHelper
 *
 * @package Panda\Support\Helpers
 *
 * @version 0.1
 */
class StringHelper
{
    /**
     * Check if a given string contains a given substring.
     *
     * @param string       $haystack
     * @param string|array $needles
     *
     * @return bool
     */
    public static function contains($haystack, $needles)
    {
        if (empty($haystack)) {
            return false;
        }

        if (!is_array($needles)) {
            return mb_strpos($haystack, $needles) !== false;
        }

        foreach ((array)$needles as $needle) {
            if ($needle != '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }
}
