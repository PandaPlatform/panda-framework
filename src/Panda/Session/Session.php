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

namespace Panda\Session;

use Panda\Contracts\Init\Initializer;
use Panda\Http\Request;

/**
 * Session Manager. Handles all session storage processes.
 *
 * @package Panda\Session
 * @version 0.1
 */
class Session implements Initializer
{
    /**
     * @type SessionHandler
     */
    private $handler;

    /**
     * The session's expiration time (in seconds).
     *
     * @type integer
     */
    const EXPIRE = 18000;

    /**
     * Session constructor.
     *
     * @param SessionHandler $handler
     */
    public function __construct(SessionHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Init session.
     *
     * @param Request $request
     */
    public function init(Request $request)
    {
        $this->handler->startSession($request);
    }

    /**
     * @return SessionHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param SessionHandler $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }
}

?>