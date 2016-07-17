<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Localization\Translation;

use Panda\Contracts\Localization\FileProcessor;
use Panda\Support\Helpers\ArrayHelper;

/**
 * Class JsonProcessor
 *
 * @package Panda\Localization\Translation
 *
 * @version 0.1
 */
class JsonProcessor implements FileProcessor
{
    /**
     * @var string
     */
    protected $baseDirectory;

    /**
     * @var array
     */
    protected static $translations;

    /**
     * Get a translation value.
     *
     * @param string $key
     * @param mixed  $default
     * @param string $locale
     *
     * @return mixed
     */
    public function get($key, $locale, $default = null)
    {
        // Check key
        if (empty($key)) {
            return $default;
        }

        // Load translations
        $this->loadTranslations($locale);

        // Return translation
        $array = (static::$translations[$locale] ?: []);

        return ArrayHelper::get($array, $key, $default, true);
    }

    /**
     * Set the base literals directory.
     *
     * @param string $directory
     *
     * @return $this
     */
    public function setBaseDirectory($directory)
    {
        $this->baseDirectory = $directory;

        return $this;
    }

    /**
     * Load translations from file.
     *
     * @param string $locale
     *
     * @return $this
     */
    private function loadTranslations($locale)
    {
        if (empty(static::$translations[$locale])) {
            $filePath = $this->baseDirectory . DIRECTORY_SEPARATOR . $locale . '.json';
            $fileContents = file_get_contents($filePath);
            static::$translations[$locale] = json_decode($fileContents, true);
        }

        return $this;
    }
}
