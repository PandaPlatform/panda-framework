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

namespace Panda\Http;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Http Request Handler.
 *
 * @package Panda\Http
 * @version 0.1
 */
class Request extends SymfonyRequest
{
    /**
     * Capture the incoming request, including all the
     * information we gan get.
     *
     * @return $this
     */
    public function capture()
    {
        return $this;
    }
}