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

namespace Panda\Debug;

use Panda\Contracts\Init\Initializer;
use Panda\Http\Request;

/**
 * Class Debugger
 *
 * @package Panda\Debug
 * @version 0.1
 */
class Debugger implements Initializer
{
    /**
     * Init session.
     *
     * @param Request $request
     */
    public function init($request)
    {
        // Set error reporting
        error_reporting(E_ALL & ~(E_STRICT | E_NOTICE | E_WARNING));

        // Set Server to display errors
        if ($request->cookies->get("pdebug")) {
            ini_set('display_errors', 'On');
        }
    }
}

?>