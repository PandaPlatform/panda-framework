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

use Symfony\Component\HttpFoundation\RedirectResponse;
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
     * Create a redirect response.
     *
     * @param string $url
     * @param int    $status
     * @param array  $headers
     *
     * @return RedirectResponse
     */
    public function redirect($url = '', $status = 302)
    {
        // Set headers
        $this->setStatusCode($status);
        $this->headers->set('Location', $url);

        // Set special content to redirect
        $this->setContent(
            sprintf('<!DOCTYPE html>
<html><head><meta charset="UTF-8" /><meta http-equiv="refresh" content="0;url=%1$s" /></head><body></body></html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8')));

        return $this;
    }

    /**
     * Set the response content.
     *
     * @param mixed $content
     *
     * @return $this
     */
    public function setContent($content)
    {
        return parent::setContent($content);
    }
}