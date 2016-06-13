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

namespace Panda\Support\Helpers;

/**
 * Class ArrayHelper
 *
 * @package Panda\Support\Helpers
 * @version 0.1
 */
class ArrayHelper
{
    /**
     * Get an item from an array.
     *
     * @param array  $array
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function get($array, $key, $default = null)
    {
        if (is_null($key)) {
            return $array;
        }

        if (!isset($array[$key])) {
            return $default;
        }

        return $array[$key];
    }

    /**
     * Filter array elements with a given callback function.
     *
     * @param  array         $array
     * @param  callable|null $callback
     * @param  mixed         $default
     *
     * @return mixed
     */
    public static function filter($array, callable $callback = null, $default = null)
    {
        if (is_null($callback)) {
            return empty($array) ? EvalHelper::evaluate($default) : reset($array);
        }
        foreach ($array as $key => $value) {
            if (call_user_func($callback, $key, $value)) {
                return $value;
            }
        }

        return EvalHelper::evaluate($default);
    }
}