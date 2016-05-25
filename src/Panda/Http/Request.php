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

use Panda\Helpers\ArrayHelper;
use Panda\Helpers\StringHelper;
use Symfony\Component\HttpFoundation\ParameterBag;
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
     * The decoded JSON content for the request.
     *
     * @var string
     */
    protected $json;

    /**
     * Capture the incoming request, including all the
     * information we gan get.
     *
     * @return Request
     */
    public static function capture()
    {
        static::enableHttpMethodParameterOverride();

        return static::createRequest(SymfonyRequest::createFromGlobals());
    }

    /**
     * Create a Panda request from a captured Symfony instance.
     *
     * @param  SymfonyRequest $request
     *
     * @return Request
     */
    public static function createRequest(SymfonyRequest $request)
    {
        // Check given instance
        if ($request instanceof static) {
            return $request;
        }

        // Get request content
        $content = $request->content;
        $request = (new static)->duplicate(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $request->files->all(), $request->server->all()
        );
        $request->content = $content;
        $request->request = $request->getInputSource();

        return $request;
    }

    /**
     * Get the input source for the request.
     *
     * @return ParameterBag
     */
    protected function getInputSource()
    {
        if ($this->isJson()) {
            return $this->getPayloadJSON();
        }

        return $this->getMethod() == 'GET' ? $this->query : $this->request;
    }

    /**
     * Get an input item from the request.
     *
     * @param  string            $key
     * @param  string|array|null $default
     *
     * @return string|array
     */
    public function getInputValue($key = null, $default = null)
    {
        $input = $this->getInputSource()->all() + $this->query->all();

        return ArrayHelper::get($input, $key, $default);
    }

    /**
     * Determine if the request is sending JSON.
     *
     * @return bool
     */
    public function isJson()
    {
        return StringHelper::contains($this->header('CONTENT_TYPE'), ['/json', '+json']);
    }

    /**
     * Retrieve a header from the request.
     *
     * @param  string            $key
     * @param  string|array|null $default
     *
     * @return string|array
     */
    public function header($key = null, $default = null)
    {
        return $this->getObjectValue('headers', $key, $default);
    }

    /**
     * Retrieve a parameter item from a given source.
     *
     * @param  string            $source
     * @param  string            $key
     * @param  string|array|null $default
     *
     * @return string|array
     */
    protected function getObjectValue($source, $key, $default)
    {
        if (is_null($key)) {
            return $this->$source->all();
        }

        return $this->$source->get($key, $default);
    }

    /**
     * Get the JSON payload for the request.
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    public function getPayloadJSON($key = null, $default = null)
    {
        if (!isset($this->json)) {
            $this->json = new ParameterBag((array)json_decode($this->getContent(), true));
        }

        if (is_null($key)) {
            return $this->json;
        }

        return ArrayHelper::get($this->json->all(), $key, $default);
    }
}