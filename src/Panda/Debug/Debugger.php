<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Debug;

use Panda\Contracts\Bootstrapper;
use Panda\Foundation\Application;
use Panda\Http\Request;

/**
 * Class Debugger
 *
 * @package Panda\Debug
 *
 * @version 0.1
 */
class Debugger implements Bootstrapper
{
    /**
     * @var Application
     */
    private $app;

    /**
     * Environment constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Init session.
     *
     * @param Request $request
     */
    public function boot($request)
    {
        // Set error reporting
        error_reporting(E_ALL & ~(E_NOTICE | E_WARNING | E_DEPRECATED));

        // Set framework to display errors
        if ($request->get($key = 'pdebug', $default = null, $includeCookies = true) || $this->app->get('env') == 'development') {
            ini_set('display_errors', 'On');
        } else {
            ini_set('display_errors', 'Off');
        }
    }
}
