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

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * Http Response Handler.
 *
 * @package Panda\Http
 * @version 0.1
 */
class Response extends SymfonyResponse
{
    /**
     * Generate the response and send to the output buffer.
     */
    public function send()
    {

    }
}