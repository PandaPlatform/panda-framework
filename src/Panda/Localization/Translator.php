<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Localization;

use Exception;
use Panda\Contracts\Configuration\ConfigurationHandler;
use Panda\Contracts\Localization\FileProcessor;
use Panda\Foundation\Application;

/**
 * Class Locale
 *
 * @package Panda\Localization
 *
 * @version 0.1
 */
class Translator
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ConfigurationHandler
     */
    private $config;

    /**
     * @var FileProcessor
     */
    protected $processor;

    /**
     * Translator constructor.
     *
     * @param Application          $app
     * @param ConfigurationHandler $config
     * @param FileProcessor        $processor
     */
    public function __construct(Application $app, ConfigurationHandler $config, FileProcessor $processor)
    {
        // Initialize fields
        $this->app = $app;
        $this->config = $config;
        $this->processor = $processor;

        // Initialize base directory for the processor
        $this->processor->setBaseDirectory($this->app->getBasePath() . DIRECTORY_SEPARATOR . $config->get('paths.lang.base_dir'));
    }

    /**
     * Get a translation value.
     * If there is no value for the given locale, it tries to fallback to default locale.
     * If the default value is null and no translation is found, it throws Exception.
     *
     * @param string $key
     * @param string $package
     * @param string $locale
     * @param mixed  $default
     *
     * @return string
     *
     * @throws Exception
     */
    public function translate($key, $package = 'default', $locale = '', $default = null)
    {
        // Try to get normal translation
        $locale = ($locale ?: Locale::get());
        $defaultLocale = Locale::getDefault();
        try {
            // Get translation with null as default to allow fallback to default locale
            $translation = $this->processor->get($key, $locale, $package, null);
        } catch (Exception $ex) {
            // Fallback to default, if empty
            try {
                $translation = $this->processor->get($key, $defaultLocale, $package, $default);
            } catch (Exception $ex2) {
                $translation = $default;
            }
        } finally {
            if (is_null($default) && $translation === $default) {
                throw new Exception('The translation for [' . $package . ']->[' . $key . '] is not found in any locale [' . $locale . ', ' . $defaultLocale . '].');
            }
        }


        return $translation;
    }

    /**
     * @param FileProcessor $processor
     */
    public function setProcessor($processor)
    {
        $this->processor = $processor;
    }
}
