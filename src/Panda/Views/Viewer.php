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

namespace Panda\Views;

use InvalidArgumentException;
use Panda\Foundation\Application;
use Panda\Http\Request;

/**
 * Class Viewer
 * Manages application views and renders their content
 *
 * @package Panda\Views
 * @version 0.1
 */
class Viewer
{
    /**
     * @type Application
     */
    private $app;

    /**
     * @type Request
     */
    private $request;

    /**
     * Viewer constructor.
     *
     * @param Application $app
     * @param Request     $request
     */
    public function __construct(Application $app, Request $request)
    {
        $this->app = $app;
        $this->request = $request;
    }

    /**
     * Load a view and return its content.
     *
     * @param string $name
     *
     * @return string
     */
    public function load($name)
    {
        // Get view full path
        $viewFolder = $this->getViewFolder($name);

        // Check view file
        $viewFile = $this->getViewFile($viewFolder);

        // Render the view file
        return $this->render($viewFile);
    }

    /**
     * Render the view output.
     *
     * @param string $viewFile
     *
     * @return string
     */
    public function render($viewFile)
    {
        // Try to load the view file and return the output
        if (empty($viewFile)) {
            throw new InvalidArgumentException("The view file given is a not valid view.");
        }

        return @include($viewFile);
    }

    /**
     * Get the view's base folder.
     *
     * @param string $name
     *
     * @return string
     */
    private function getViewFolder($name)
    {
        return $this->app->getViewsPath() . DIRECTORY_SEPARATOR . $name . ".view" . DIRECTORY_SEPARATOR;
    }

    /**
     * Get the view file to be rendered for output.
     *
     * @param string $viewFolder
     *
     * @return null|string
     */
    private function getViewFile($viewFolder)
    {
        // Set base name
        $baseName = $viewFolder . DIRECTORY_SEPARATOR . "view";

        $viewFile = (file_exists($baseName . ".php") ? $baseName . ".php" : $baseName . ".html");
        $viewFile = (file_exists($viewFile) ? $viewFile : null);

        return $viewFile;
    }
}