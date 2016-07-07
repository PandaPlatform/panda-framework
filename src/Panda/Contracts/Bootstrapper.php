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

namespace Panda\Contracts;

use Panda\Http\Request;

/**
 * Interface Bootstrapper
 *
 * @package Panda\Contracts
 *
 * @version 0.1
 */
interface Bootstrapper
{
    /**
     * Boot the bootstrapper.
     *
     * @param Request $request
     */
    public function boot($request);
}