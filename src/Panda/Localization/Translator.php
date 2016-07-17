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
     *
     * @param string $key
     * @param string $locale
     *
     * @return string
     */
    public function translate($key, $locale = '')
    {
        // Try to get normal translation
        $locale = ($locale ?: Locale::get());
        $translation = $this->processor->get($key, $locale, null);

        // Fallback to default, if empty
        $defaultLocale = Locale::getDefault();
        $translation = ($translation ?: $this->processor->get($key, $defaultLocale, null));

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
