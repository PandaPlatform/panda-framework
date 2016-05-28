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
     * @type bool
     */
    private $executable = false;

    /**
     * @type string
     */
    protected $output;

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
     * @return $this
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
     * @return $this
     */
    public function render($viewFile)
    {
        // Try to load the view file and return the output
        if (empty($viewFile)) {
            throw new InvalidArgumentException("The view file given is a not valid view.");
        }

        // Load the view file
        if ($this->executable) {
            $this->output = @include($viewFile);
        } else {
            $this->output = file_get_contents($viewFile);
        }

        return $this;
    }

    /**
     * Outputs the view's html to the buffer using echo.
     */
    public function out()
    {
        echo $this->output;
    }

    /**
     * Get the view output instead of sending it to buffer.
     *
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
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
        return $this->app->getViewsPath() . DIRECTORY_SEPARATOR . $name . ".view";
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

        // Select the view file
        $viewFile = (file_exists($baseName . ".php") ? $baseName . ".php" : $baseName . ".html");
        $viewFile = (file_exists($viewFile) ? $viewFile : null);

        // Check if the file is executable (php)
        if (preg_match('/\.php$/', $viewFile)) {
            $this->executable = true;
        }

        return $viewFile;
    }
}