<?php

/*
 * This file is part of the Panda framework component.
 *
 * (c) Ioannis Papikas <papikas.ioan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Panda\Database;

use Exception;
use Panda\Contracts\Database\ConnectionHandler;

/**
 * MySQL Connection Handler
 *
 * @version 0.1
 */
class Connection
{
    /**
     * @type ConnectionHandler
     */
    protected $handler;

    /**
     * The transaction error.
     *
     * @type string
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
     * @return bool|False
     * @throws Exception
     */
    public function execute($query, $attr = array(), $commit = true)
    {
        // Clear error message
        $this->error = "";

        // Set query attributes
        foreach ($attr as $key => $value) {
            // Escape value
            $value = $this->handler->escape($value);

            // Replace escaped value
            $query = str_replace("{" . $key . "}", $value, $query);
        }

        try {
            // Execute query to handler
            return $this->handler->query($query, $commit);
        } catch (Exception $ex) {
            // Store error message
            $this->error = $ex->getMessage();

            // Log Exception Message
            // logger::getInstance()->log("Query Execution to [".$this->database."] at ".$this->host." failed: ".$this->error, logger::ERROR, $query);

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