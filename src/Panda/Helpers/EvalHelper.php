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

namespace Panda\Helpers;

use Closure;

/**
 * Class EvalHelper
 *
 * @package Panda\Helpers
 * @version 0.1
 */
class EvalHelper
{
    /**
     * Evaluate the given value and see if it's a Closure or a simple value.
     *
     * @param  mixed $value
     *
     * @return mixed
     */
    public static function evaluate($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}

?>