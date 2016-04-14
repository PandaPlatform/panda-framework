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

namespace Panda\Session;

/*
use \ESS\Environment\cookies;
use \ESS\Environment\url;
*/
/**
 * Session Manager
 * 
 * Handles all session storage processes.
 * 
 * @version	0.2-1
 * @created	November 4, 2014, 8:13 (GMT)
 * @updated	October 20, 2015, 11:07 (BST)
 */
class Session
{
	/**
	 * The session's expiration time (in seconds).
	 * 
	 * @type	integer
	 */
	const EXPIRE = 18000;
	
	/**
	 * Init session.
	 * 
	 * @param	array	$options
	 * 		A set of options like the session_id etc.
	 * 
	 * @return	void
	 */
	public static function init($options = array())
	{
		// Check if in secure environment (by application) and skip
		if (importer::secure())
			return;
		
		// Start session
		self::start();
		
		// Initialise the session timers
		self::setTimers();
		
		// Validate this session
		self::validate();
		
		// Set session options
		self::setOptions($options);
	}
	
	/**
	 * Get a session variable value.
	 * 
	 * @param	string	$name
	 * 		The name of the variable.
	 * 
	 * @param	string	$default
	 * 		The value that will be returned if the variable doesn't exist.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	string
	 * 		The session value.
	 */
	public static function get($name, $default = NULL, $namespace = 'default')
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);

		if (isset($_SESSION[$namespace][$name]))
			return $_SESSION[$namespace][$name];
			
		return $default;
	}
	
	/**
	 * Set a session variable value.
	 * 
	 * @param	string	$name
	 * 		The name of the variable.
	 * 
	 * @param	string	$value
	 * 		The value with which the variable will be set.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	mixed
	 * 		The old value of the variable, or NULL if not set.
	 */
	public static function set($name, $value = NULL, $namespace = 'default')
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);

		$old = isset($_SESSION[$namespace][$name]) ? $_SESSION[$namespace][$name] : NULL;

		if (NULL === $value)
			unset($_SESSION[$namespace][$name]);
		else
			$_SESSION[$namespace][$name] = $value;

		return $old;
	}
	
	/**
	 * Check if there is a session variable
	 * 
	 * @param	string	$name
	 * 		The variable name.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	boolean
	 * 		True if the variable is set, false otherwise.
	 */
	public static function has($name, $namespace = 'default')
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);

		return isset($_SESSION[$namespace][$name]);
	}
	
	/**
	 * Deletes a session variable
	 * 
	 * @param	string	$name
	 * 		The variable name.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	mixed
	 * 		The old value of the variable, or NULL if not set.
	 * 
	 * @deprecated	Use remove() instead.
	 */
	public static function clear($name, $namespace = 'default')
	{
		return self::remove($name, $namespace);
	}
	
	/**
	 * Removes a session variable.
	 * 
	 * @param	string	$name
	 * 		The variable name.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	mixed
	 * 		The old value of the variable, or NULL if not set.
	 */
	public static function remove($name, $namespace = 'default')
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);

		$value = NULL;
		if (isset($_SESSION[$namespace][$name]))
		{
			$value = $_SESSION[$namespace][$name];
			unset($_SESSION[$namespace][$name]);
		}

		return $value;
	}
	
	/**
	 * Delete a set of session variables under the same namespace
	 * 
	 * @param	string	$namespace
	 * 		The namespace to be cleared.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function clearSet($namespace)
	{
		// Get SESSION Namespace
		$namespace = self::getNS($namespace);
			
		unset($_SESSION[$namespace]);
		return TRUE;
	}
	
	/**
	 * Get session name.
	 * 
	 * @return	string
	 * 		The session name.
	 */
	public static function getName()
	{
		return session_name();
	}
	
	/**
	 * Get session id
	 * 
	 * @return	integer
	 * 		The session unique id.
	 */
	public static function getID()
	{
		return session_id();
	}
	
	/**
	 * Get session id
	 * 
	 * @return	integer
	 * 		The session unique id.
	 * 
	 * @deprecated	Use getID() instead.
	 */
	public static function get_id()
	{
		return self::getID();
	}
	
	/**
	 * Destroy session.
	 * 
	 * @return	boolean
	 * 		True on success, false on failure.
	 */
	public static function destroy()
	{
		$sessionCookie = cookies::get(session_name());
		if (!empty($sessionCookie))
			cookies::remove(session_name());

		session_unset();
		session_destroy();

		return TRUE;
	}
	
	/**
	 * Return the in-memory size of the session ($_SESSION) array.
	 * 
	 * @return	integer
	 * 		The memory size in length.
	 */
	public static function getSize()
	{
		return strlen(serialize($_SESSION));
	}
	
	/**
	 * Set the validation timers.
	 * 
	 * @param	boolean	$forceRegenerate
	 * 		Forces the timers to regenerate (in case of an expiration or something).
	 * 
	 * @return	void
	 */
	private static function setTimers($forceRegenerate = FALSE)
	{
		$start = time();
		
		// If there is no starting point, restart all over again
		if (!self::has('timer.start', "session") || $forceRegenerate)
		{
			self::set('timer.start', $start, "session");
			self::set('timer.last', $start, "session");
			self::set('timer.now', $start, "session");
		}

		// Set current timers
		self::set('timer.last', self::get('timer.now', NULL, "session"), "session");
		self::set('timer.now', time(), "session");
	}
	
	/**
	 * Set the session options.
	 * It supports only the session id for now.
	 * 
	 * @param	array	$options
	 * 		An array of options for the session.
	 * 		It supports only the session id (id) for now.
	 * 
	 * @return	void
	 */
	private static function setOptions(array $options)
	{
		// Set name
		if (isset($options['id']))
			session_id(md5($options['id']));

		// Sync the session maxlifetime
		ini_set('session.gc_maxlifetime', self::EXPIRE);
	}
	
	/**
	 * Start the session.
	 * 
	 * @return	void
	 */
	private static function start()
	{
		register_shutdown_function('session_write_close');
		session_cache_limiter('none');
		
		// Set Session cookie params
		$sessionCookieParams = session_get_cookie_params();
		$rootDomain = url::getDomain();
		
		session_set_cookie_params(
			$sessionCookieParams["lifetime"], 
			$sessionCookieParams["path"], 
			$rootDomain, 
			$sessionCookieParams["secure"], 
			$sessionCookieParams["httponly"]
		);
		
		// Set name
		session_name("ss");

		// Session start
		session_start();
	}
	
	/**
	 * Validate the session and reset if necessary.
	 * 
	 * @return	void
	 */
	protected static function validate()
	{
		// Regenerate session if gone too long and reset timers
		if ((time() - self::get('timer.start', NULL, "session") > self::EXPIRE))
		{
			session_regenerate_id(true);
			self::setTimers(TRUE);
		}
		
		// Destroy session if expired
		if ((time() - self::get('timer.last', NULL, "session") > self::EXPIRE))
			self::destroy();
	}
	
	/**
	 * Create the namespace string.
	 * 
	 * @param	string	$namespace
	 * 		The namespace of the session variable.
	 * 
	 * @return	string
	 * 		The namespace string value.
	 */
	private static function getNS($namespace)
	{
		// Add prefix to namespace to avoid collisions.
		return "__".strtoupper($namespace);
	}
}
?>