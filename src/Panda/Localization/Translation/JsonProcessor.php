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

use Exception;
use Panda\Contracts\Localization\FileProcessor;
use Panda\Support\Helpers\ArrayHelper;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

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
     * If the default value is null and no translation is found, it throws Exception.
     *
     * @param string $key
     * @param string $locale
     * @param string $package
     * @param mixed  $default
     *
     * @return mixed
     *
     * @throws Exception
     */
    public function get($key, $locale, $package = 'default', $default = null)
    {
        // Check key
        if (empty($key)) {
            return $default;
        }

        // Normalize group and load translations
        $package = ($package ?: 'default');
        try {
            $this->loadTranslations($locale, $package);

            // Return translation
            $array = (static::$translations[$locale] ?: []);

            $value = ArrayHelper::get($array, $key, $default, true);
        } catch (Exception $ex) {
            $value = $default;
        } finally {
            if (is_null($default) && $value === $default) {
                throw new Exception('The [' . $locale . '] translation for [' . $package . ']->[' . $key . '] is not found.');
            }
        }

        return $value;
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
     * @param string $package
     *
     * @return $this
     *
     * @throws FileNotFoundException
     */
    private function loadTranslations($locale, $package)
    {
        if (empty(static::$translations[$locale])) {
            // Get full file path
            $fileName = $locale . DIRECTORY_SEPARATOR . $package . '.json';
            $filePath = $this->baseDirectory . DIRECTORY_SEPARATOR . $fileName;

            // Check if is valid and load translations
            if (is_file($filePath)) {
                $fileContents = file_get_contents($filePath);
                static::$translations[$locale] = json_decode($fileContents, true);
            } else {
                throw new FileNotFoundException($fileName);
            }
        }

        return $this;
    }
}
