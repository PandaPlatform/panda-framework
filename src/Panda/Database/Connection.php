<?php

/*
 * This file is part of the Panda framework.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Panda\Database;

use Exception;
use Panda\Contracts\Database\ConnectionHandler;

/**
 * Database Connection Interface
 *
 * @package Panda\Database
 * @version 0.1
 */
class Connection
{
    /**
     * @var ConnectionHandler
     */
    protected $handler;

    /**
     * The transaction error.
     *
     * @var string
     */
    protected $error;

    /**
     * Create a new database connection instance.
     *
     * @param ConnectionHandler $handler The database connection handler.
     */
    public function __construct(ConnectionHandler $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Executes a query to the database.
     * It supports multiple queries separated with semicolon.
     * It supports parameters using the {param} annotation.
     *
     * @param string $query  The query to be executed.
     * @param array  $attr   An associative array of the query attributes.
     *                       The keys of the array will replace the query attributes with the array key values.
     *                       It is empty by default.
     * @param bool   $commit Whether to commit the transaction  after the last query or not.
     *                       It is True by default.
     *
     * @throws Exception
     *
     * @return bool
     */
    public function execute($query, $attr = [], $commit = true)
    {
        // Clear error message
        $this->error = '';

        // Set query attributes
        foreach ($attr as $key => $value) {
            // Escape value
            $value = $this->handler->escape($value);

            // Replace escaped value
            $query = str_replace('{' . $key . '}', $value, $query);
        }

        try {
            // Execute query to handler
            return $this->handler->query($query, $commit);
        } catch (Exception $ex) {
            // Store error message
            $this->error = $ex->getMessage();

            // Re-throw exception
            throw $ex;
        }
    }

    /**
     * @return ConnectionHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param ConnectionHandler $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }
}
