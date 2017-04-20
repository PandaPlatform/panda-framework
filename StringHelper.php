<?php

/*
 * This file is part of the Panda Helpers Package.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Helpers;

use InvalidArgumentException;

/**
 * Class StringHelper
 * @package Panda\Helpers
 */
class StringHelper
{
    /**
     * Check if a given string contains a given substring.
     *
     * @param string       $haystack
     * @param string|array $needle
     *
     * @return bool
     * @throws InvalidArgumentException
     */
    public static function contains($haystack, $needle)
    {
        // Check arguments
        if (empty($haystack)) {
            throw new InvalidArgumentException(__METHOD__ . ': The given haystack cannot be empty.');
        }
        if (empty($needle)) {
            throw new InvalidArgumentException(__METHOD__ . ': The given needle cannot be empty.');
        }

        // Needle is string
        if (!is_array($needle)) {
            return mb_strpos($haystack, $needle) !== false;
        }

        // Needle is array, check if haystack contains all items
        foreach ((array)$needle as $str_needle) {
            if (!empty($str_needle) && mb_strpos($haystack, $str_needle) === false) {
                return false;
            }
        }

        return true;
    }
}
