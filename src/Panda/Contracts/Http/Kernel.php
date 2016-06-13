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

namespace Panda\Contracts\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface Kernel
 *
 * @package Panda\Contracts\Http
 * @version 0.1
 */
interface Kernel
{
    /**
     * Init the application for HTTP requests.
     *
     * @param Request $request
     */
    public function init(Request $request);

    /**
     * Handle an incoming HTTP request.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handle(Request $request);

    /**
     * Perform any final actions for the request lifecycle.
     *
     * @param Request  $request
     * @param Response $response
     */
    public function terminate(Request $request, Response $response);
}