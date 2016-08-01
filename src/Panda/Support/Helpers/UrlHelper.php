<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Support\Helpers;

/**
 * Class UrlHelper
 *
 * @package Panda\Support\Helpers
 *
 * @version 0.1
 */
class UrlHelper
{
    /**
     * Creates and returns a url with parameters in url encoding.
     *
     * @param string $url        The base url.
     * @param array  $parameters An associative array of parameters as key => value.
     * @param string $host
     * @param string $protocol
     *
     * @return string A well formed url.
     */
    public static function get($url, $parameters = [], $host = null, $protocol = null)
    {
        // Get current url info
        $urlInfo = self::info();

        // Build url query
        if (!empty($parameters)) {
            $url .= '?' . http_build_query($parameters);
        }

        // Get full url
        $url = rtrim($url, '?&');

        // Set protocol
        $host = $host ?: $urlInfo['host'];
        $protocol = $protocol ?: $urlInfo['protocol'];

        // Resolve URL according to system configuration
        return $protocol . '://' . self::normalize($host . '/' . $url);
    }

    /**
     * Get current domain.
     *
     * @return string
     */
    public static function getDomain()
    {
        $urlInfo = self::info();

        return $urlInfo['domain'];
    }

    /**
     * Gets the current navigation subdomain.
     *
     * @param bool $useOrigin Set True to use origin value if exists.
     *
     * @return string
     */
    public static function getSubDomain($useOrigin = true)
    {
        // Get current url info
        $urlInfo = self::info();

        // Check if there is an origin value and use that
        if (isset($urlInfo['origin']) && $useOrigin) {
            $urlInfo = self::info($urlInfo['origin']);
        }

        // Return subdomain value
        return $urlInfo['sub'];
    }

    /**
     * Gets the info of the url in an array.
     *
     * @param string $url    The url to get the information from. If the url is empty, get the current request url.
     * @param string $domain The url domain. This is given to distinguish the subdomains on the front.
     *
     * @return array The url info as follows:
     *               ['url'] = The full url page.
     *               ['protocol'] = The server protocol.
     *               ['sub'] = The navigation subdomain.
     *               ['domain'] = The host domain.
     *               ['host'] = The full host.
     *               ['params'] = An array of all url parameters by name and value.
     *               ['referer'] = The referer value, if exists.
     *               ['origin'] = The host origin value, if exists.
     */
    public static function info($url = '', $domain = '')
    {
        // Initialize url info array
        $info = [];

        // Get protocol from given url
        $protocol = (strpos($url, 'https') === 0 ? 'https' : 'http');

        // If no given url, get current
        if (empty($url)) {
            // Get protocol
            $protocol = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https' : 'http');
            $url = $protocol . '://' . $_SERVER['HTTP_HOST'] . '/' . $_SERVER['REQUEST_URI'];
            $info['referer'] = $_SERVER['HTTP_REFERER'];
            $info['origin'] = $_SERVER['HTTP_ORIGIN'];
        }

        // Normalize url
        $url = str_replace($protocol . '://', '', $url);
        $info['url'] = $protocol . '://' . $url;
        $info['protocol'] = $protocol;

        // Split for domain and subdomain
        list($path, $params) = explode('?', $url);

        // Get all host parts
        $pathParts = explode('/', $path);
        $host = $pathParts[0];

        // Check if this is an ip or a domain
        if (self::isIP($host)) {
            $sub = '';
            $domain = $host;
        } else {
            // Check if the url from the given domain
            $pattern = '/.*' . str_replace('.', '\.', $domain) . '$/i';
            if (preg_match($pattern, $host) == 1) {
                // Get subdomain
                $sub = str_replace($domain, '', $host);
                $sub = (empty($sub) ? 'www' : $sub);
                $sub = trim($sub, '.');
            } else {
                // Get host parts
                $parts = explode('.', $host);

                // Host must has at least 2 parts
                if (count($parts) < 3) {
                    $sub = 'www';
                } else {
                    $sub = $parts[0];
                    unset($parts[0]);
                }

                // Set domain
                $domain = implode('.', $parts);
            }
        }

        // Set info
        $info['host'] = $host;
        $info['sub'] = $sub;
        $info['domain'] = $domain;

        // Get parameters
        $urlParams = explode('&', $params);
        foreach ($urlParams as $up) {
            $pparts = explode('=', $up);
            if (!empty($pparts) && !empty($pparts[0])) {
                $info['params'][$pparts[0]] = $pparts[1];
            }
        }

        return $info;
    }

    /**
     * Check if the given url is in IP format.
     *
     * @param string $url
     *
     * @return bool
     */
    public static function isIP($url)
    {
        // Check if given url is ip (v4 or v6)
        return self::isIPv4($url);
    }

    /**
     * Check if the given url is an IPv4.
     *
     * @param string $url
     *
     * @return bool
     */
    private static function isIPv4($url)
    {
        return preg_match('/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/', $url);
    }

    /**
     * Normalizes a path by collapsing redundant slashes.
     *
     * @param string $url
     *
     * @return mixed
     */
    private function normalize($url)
    {
        return preg_replace('/\/{2,}/', '/', $url);
    }
}
