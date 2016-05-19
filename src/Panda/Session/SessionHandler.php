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

use Panda\Cookie\CookieJar;
use Panda\Foundation\Application;
use Panda\Http\Request;

/**
 * Session Handler
 *
 * Handles all session storage processes.
 *
 * @version 0.1
 */
class SessionHandler
{
    /**
     * @type string
     */
    private $sessionId;

    /**
     * @type string
     */
    private $sessionName;

    /**
     * @type Application
     */
    private $app;

    /**
     * SessionHandler constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Init session.
     *
     * @param Request $request
     */
    public function startSession(Request $request)
    {
        // Set session options
        //$this->setOptions($options);

        // Start session
        $this->start();

        // Initialise the session timers
        $this->setTimers();

        // Validate this session
        $this->validate();
    }

    /**
     * Set Session options for set and get.
     *
     * @param array $options An array of options for the session (id, name).
     */
    public function setOptions(array $options)
    {
        // Set session id
        if (isset($options['id']))
            session_id(md5($options['id']));

        // Set session name
        if (isset($options['name']))
            session_name($options['name']);

        // Sync the session maxlifetime
        ini_set('session.gc_maxlifetime', Session::EXPIRE);
    }

    /**
     * Get a session variable value.
     *
     * @param string $name      The name of the variable.
     * @param string $default   The value that will be returned if the variable doesn't exist.
     * @param string $namespace The namespace of the session variable.
     *
     * @return string
     */
    public function get($name, $default = null, $namespace = 'default')
    {
        // Get SESSION Namespace
        $namespace = $this->getNS($namespace);

        if (isset($_SESSION[$namespace][$name]))
            return $_SESSION[$namespace][$name];

        return $default;
    }

    /**
     * Set a session variable value.
     *
     * @param string $name      The name of the variable.
     * @param string $value     The value with which the variable will be set.
     * @param string $namespace The namespace of the session variable.
     *
     * @return mixed The old value of the variable, or NULL if not set.
     */
    public function set($name, $value = null, $namespace = 'default')
    {
        // Get SESSION Namespace
        $namespace = $this->getNS($namespace);

        $old = (isset($_SESSION[$namespace][$name]) ? $_SESSION[$namespace][$name] : null);

        if (is_null($value))
            unset($_SESSION[$namespace][$name]);
        else
            $_SESSION[$namespace][$name] = $value;

        return $old;
    }

    /**
     * Check if there is a session variable
     *
     * @param string $name      The variable name.
     * @param string $namespace The namespace of the session variable.
     *
     * @return boolean True if the variable is set, false otherwise.
     */
    public function has($name, $namespace = 'default')
    {
        // Get SESSION Namespace
        $namespace = $this->getNS($namespace);

        return isset($_SESSION[$namespace][$name]);
    }

    /**
     * Removes a session variable.
     *
     * @param string $name      The variable name.
     * @param string $namespace The namespace of the session variable.
     *
     * @return mixed The old value of the variable, or NULL if not set.
     */
    public function remove($name, $namespace = 'default')
    {
        // Get SESSION Namespace
        $namespace = $this->getNS($namespace);

        $value = null;
        if (isset($_SESSION[$namespace][$name])) {
            $value = $_SESSION[$namespace][$name];
            unset($_SESSION[$namespace][$name]);
        }

        return $value;
    }

    /**
     * Delete a set of session variables under the same namespace
     *
     * @param string $namespace The namespace to be cleared.
     *
     * @return boolean True on success, false on failure.
     */
    public function clearSet($namespace)
    {
        // Get SESSION Namespace
        $namespace = $this->getNS($namespace);

        unset($_SESSION[$namespace]);

        return true;
    }

    /**
     * Get session name.
     *
     * @return string The session name.
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * Get session id
     *
     * @return integer The session unique id.
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Destroy session.
     *
     * @return boolean True on success, false on failure.
     */
    public function destroy()
    {
        /*
        $sessionCookie = CookieJar::getInstance()->get(session_name());
        if (!empty($sessionCookie))
            CookieBox::getInstance()->remove(session_name());
*/
        session_unset();
        session_destroy();

        return true;
    }

    /**
     * Return the in-memory size of the session ($_SESSION) array.
     *
     * @return integer The memory size in length.
     */
    public function getSize()
    {
        return strlen(serialize($_SESSION));
    }

    /**
     * Set the validation timers.
     *
     * @param boolean $forceRegenerate Forces the timers to regenerate (in case of an expiration or something).
     */
    private function setTimers($forceRegenerate = false)
    {
        $start = time();

        // If there is no starting point, restart all over again
        if (!$this->has('timer.start', "session") || $forceRegenerate) {
            $this->set('timer.start', $start, "session");
            $this->set('timer.last', $start, "session");
            $this->set('timer.now', $start, "session");
        }

        // Set current timers
        $this->set('timer.last', $this->get('timer.now', null, "session"), "session");
        $this->set('timer.now', time(), "session");
    }

    /**
     * Start the session.
     *
     */
    private function start()
    {
        register_shutdown_function('session_write_close');
        session_cache_limiter('none');

        // Set Session cookie params
        $sessionCookieParams = session_get_cookie_params();
        $rootDomain = Url::getInstance()->getDomain();

        session_set_cookie_params(
            $sessionCookieParams["lifetime"],
            $sessionCookieParams["path"],
            $rootDomain,
            $sessionCookieParams["secure"],
            $sessionCookieParams["httponly"]
        );

        // Session start
        session_start();
    }

    /**
     * Validate the session and reset if necessary.
     *
     */
    protected function validate()
    {
        // Regenerate session if gone too long and reset timers
        if ((time() - $this->get('timer.start', null, "session") > Session::EXPIRE)) {
            session_regenerate_id(true);
            $this->setTimers(true);
        }

        // Destroy session if expired
        if ((time() - $this->get('timer.last', null, "session") > Session::EXPIRE))
            $this->destroy();
    }

    /**
     * Create the namespace string.
     *
     * @param string $namespace The namespace of the session variable.
     *
     * @return string The namespace string value.
     */
    private function getNS($namespace)
    {
        // Add prefix to namespace to avoid collisions.
        return "__" . strtoupper($namespace);
    }

    /**
     * @return mixed
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @param mixed $sessionId
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }
}

?>