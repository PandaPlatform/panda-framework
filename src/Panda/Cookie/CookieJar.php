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

namespace Panda\Cookie;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Cookie collection
 *
 * Creates, edits and deletes cookies.
 *
 * @version 0.1
 */
class CookieJar
{
    /**
     * The default path (if specified).
     *
     * @type string
     */
    protected $path = '/';

    /**
     * The default domain (if specified).
     *
     * @type string
     */
    protected $domain = null;

    /**
     * The default secure setting (defaults to false).
     *
     * @type boolean
     */
    protected $secure = false;

    /**
     * Stores runtime cookies until they are available at the page.
     *
     * @type    array
     */
    private static $runtimeCookies = array();

    /**
     * The Cookie singleton object.
     *
     * @type Cookie
     */
    private static $instance;

    /**
     * Get the Cookie singleton instance.
     *
     * @return Cookie
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            static::$instance = new CookieJar();
        }

        return static::$instance;
    }

    /**
     * Construct the Cookie singleton.
     */
    protected function __construct()
    {
        // Set default options
        $this->setOptions($path = "/", $domain = null, $secure = false);
    }

    /**
     * Set cookies options for set and get.
     *
     * @param string  $path The cookie path.
     *
     * @param string  $domain
     *                      The cookie domain.
     *                      Leave empty to get the current domain.
     *                      It is Null by default.
     *
     * @param boolean $secure
     *                      Indicates that the cookie should only be transmitted over a secure HTTPS connection from
     *                      the client. It is False by default.
     */
    public function setOptions($path = "/", $domain = null, $secure = false)
    {
        $this->path = $path;
        $this->domain = (empty($domain) ? "." /*. Url::getDomain()*/ : $domain);
        $this->secure = $secure;
    }

    /**
     * Set a new cookie or update an existing one.
     * It uses the php's setcookie function with preset values for domain and paths.
     *
     * @param string  $name  The cookie's name.
     * @param string  $value The cookie's value.
     * @param integer $expiration
     *                       The expiration of the cookie in seconds.
     *                       If set to 0, the cookie will expire at the end of the session.
     *                       If set to <0 the cookie will be removed. You can use remove() instead.
     *
     * @param boolean $httpOnly
     *                       When TRUE the cookie will be made accessible only through the HTTP protocol.
     *                       This means that the cookie won't be accessible by scripting languages, such as JavaScript.
     *
     * @return boolean True on success, False on failure.
     *
     * @throws InvalidArgumentException
     */
    public function set($name, $value, $expiration = 0, $httpOnly = false)
    {
        // Check cookie
        if (empty($name)) {
            throw new InvalidArgumentException("Trying to create cookie with in empty name.");
        }

        // Set cookie params
        $expiration = ($expiration == 0 ? $expiration : time() + $expiration);

        // Set cookie
        if (setcookie($name, $value, $expiration, $this->path, $this->domain, ($this->secure ? 1 : 0), ($httpOnly ? 1 : 0))) {
            // Set engine var
            if ($expiration >= 0) {
                static::$runtimeCookies[$name] = $value;
            } else {
                unset(static::$runtimeCookies[$name]);
            }

            // Return
            return true;
        }

        return false;
    }

    /**
     * Get the value of a cookie.
     *
     * @param string $name The cookie's name.
     *
     * @return mixed The cookie value or NULL if cookie doesn't exist.
     */
    public function get($name)
    {
        // Get runtime and page cookie values
        $runtimeCookie = static::$runtimeCookies[$name];
        $pageCookie = (isset($_COOKIE[$name]) ? $_COOKIE[$name] : null);

        // Return the newest
        return (empty($runtimeCookie) ? $pageCookie : $runtimeCookie);
    }

    /**
     * Remove a cookie.
     *
     * @param string $name The cookie's name.
     *
     * @return boolean True on success, false on failure.
     */
    public function remove($name)
    {
        // Set cookie and return true
        try {
            if ($this->set($name, null, -3600)) {
                return true;
            }
        } catch (InvalidArgumentException $ex) {
            // Cookie doesn't exist either way
        }

        // Return FALSE
        return false;
    }
}

?>