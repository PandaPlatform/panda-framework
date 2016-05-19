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

namespace Panda\Foundation\Init;

use Panda\Foundation\Application;

/**
 * Panda initializer.
 *
 * @version 0.1
 */
class Initializer
{
    /**
     * Initialize the given panda application.
     *
     * @param Application $app
     */
    public function initialize(Application $app)
    {
        $app->init();
    }
}