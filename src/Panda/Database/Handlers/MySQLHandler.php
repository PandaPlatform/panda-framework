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

namespace Panda\Database\Handlers;

use Exception;
use mysqli;
use mysqli_result;
use Panda\Contracts\Database\ConnectionHandler;

/**
 * Class MySQLHandler
 *
 * @package Panda\Database\Handlers
 * @version 0.1
 */
class MySQLHandler extends mysqli implements ConnectionHandler
{
    /**
     * Start a database transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function beginTransaction()
    {
        return $this->begin_transaction();
    }

    /**
     * Commit the current transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function commitTransaction()
    {
        return $this->commit();
    }

    /**
     * Rollback the current transaction.
     *
     * @return bool True on success, false on failure.
     */
    public function rollbackTransaction()
    {
        return $this->rollback();
    }

    /**
     * Performs a query on the database.
     * Executes one or multiple queries which are concatenated by a semicolon.
     *
     * @param string $query
     * @param bool   $commit
     *
     * @return mixed False on failure. For successful SELECT, SHOW, DESCRIBE or EXPLAIN queries mysqli_query() will
     *               return a mysqli_result object. For other successful queries mysqli_query() will return TRUE.
     *
     * @throws Exception
     */
    public function query($query, $commit = true)
    {
        // Connect to database
        $this->connect();

        // Disable autocommit for transaction
        $this->autocommit(false);

        // Begin transaction (this also enables table locking for security)
        $this->beginTransaction();

        // Execute all transaction queries
        $result = $this->multi_query($query);

        // Commit Transaction
        if ($commit)
            $this->commitTransaction();

        // Close connection
        $this->close();

        // Return final result
        return $result;
    }

    /**
     * Fetch a result row as an associative array.
     *
     * @param mysqli_result $resource The mysqli result resource.
     * @param bool          $full     Set to True to fetch all the rows.
     *
     * @return array The fetched array with one or all the rows. If the result is one, it's not in an array.
     */
    public function fetch($resource, $full = false)
    {
        // Check if we need all the rows
        if ($full) {
            return $resource->fetch_all();
        }

        // Get only the first row
        return $resource->fetch_assoc();
    }

    /**
     * Escapes special characters in a string for use in an SQL statement, taking into account the current charset of
     * the connection.
     *
     * @param resource $resource
     *
     * @return string The escaped string.
     */
    public function escape($resource)
    {
        $this->escape($resource);
    }
}