<?php

/*
 * This file is part of the Panda framework Foundation component.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Panda\Cookies;

/*
use \ESS\Environment\url;
*/

/**
 * Cookie controller object
 * 
 * Creates, edits and deletes cookies.
 * 
 * @version	0.1
 */
class Cookie
{
	/**
     * The default path (if specified).
     *
     * @var string
     */
    protected $path = '/';

    /**
     * The default domain (if specified).
     *
     * @var string
     */
    protected $domain = null;

    /**
     * The default secure setting (defaults to false).
     *
     * @var boolean
     */
    protected $secure = false;

	/**
	 * Stores runtime cookies until they are available at the page.
	 * 
	 * @var	array
	 */
	private static $runtimeCookies = array();

	/**
	 * Initialize the cookie controller.
	 * 
	 * @param	string	$path
	 * 		The cookie path.
	 * 
	 * @param	string	$domain
	 * 		The cookie domain.
	 *		Leave empty to get the current domain.
	 *		It is NULL by default.
	 * 
	 * @param	integer	$secure
	 * 		Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client.
	 *		It is FALSE by default.
	 * 
	 * @return	void
	 */
	public function __construct($path = "/", $domain = null, $secure = false)
	{
		$this->path = $path;
		$this->domain = (empty($domain) ? ".".url::getDomain() : $domain);
		$this->secure = $secure;
	}
	
	/**
	 * Create a new cookie or update an existing one.
	 * It uses the php's setcookie function with preset values for domain and paths.
	 * 
	 * @param	string	$name
	 * 		The cookie's name.
	 * 
	 * @param	string	$value
	 * 		The cookie's value.
	 * 
	 * @param	integer	$expiration
	 * 		The expiration of the cookie in seconds.
	 * 		If set to 0, the cookie will expire at the end of the session.
	 * 		If set to <0 the cookie will be removed. You can use remove() instead.
	 * 
	 * @param	boolean	$httpOnly
	 * 		When TRUE the cookie will be made accessible only through the HTTP protocol.
	 * 		This means that the cookie won't be accessible by scripting languages, such as JavaScript.
	 * 
	 * @param	boolean	$secure
	 * 		Indicates that the cookie should only be transmitted over a secure HTTPS connection from the client.
	 * 
	 * @return	boolean
	 * 		TRUE on success, FALSE on failure.
	 *
	 * @throws	InvalidArgumentException
	 */
	public function create($name, $value, $expiration = 0, $httpOnly = false)
	{
		// Check cookie
		if (empty($name)) {
			throw new \InvalidArgumentException("Trying to create cookie with in empty name.");
		}
			
		// Set cookie params
		$expiration = ($expiration == 0 ? $expiration : time() + $expiration);
		
		// Set cookie
		if (setcookie($name, $value, $expiration, $this->path, $this->domain, ($this->secure ? 1 : 0), ($httpOnly ? 1 : 0))) {
			// Set engine var
			if ($expiration >= 0) {
				static::$runtimeCookies[$name] = $value;
			}
			else {
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
	 * @param	string	$name
	 * 		The cookie's name.
	 * 
	 * @return	mixed
	 * 		The cookie value or NULL if cookie doesn't exist.
	 */
	// @todo get cookies from the request object
	public function get($name)
	{
		// Get runtime and page cookie values
		$runtimeCookie = static::$runtimeCookies[$name];
		$pageCookie = (isset($_COOKIE[$name]) ? $_COOKIE[$name] : NULL);
		
		// Return the newest
		return (empty($runtimeCookie) ? $pageCookie : $runtimeCookie);
	}
	
	/**
	 * Remove a cookie.
	 * 
	 * @param	string	$name
	 * 		The cookie's name.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public function remove($name)
	{
		// Set cookie and return TRUE
		try {
			if ($this->set($name, null, - 3600)) {
				return true;
			}
		} catch (InvalidArgumentException $ex) {

		}
		
		// Return FALSE
		return false;
	}	
}

?>