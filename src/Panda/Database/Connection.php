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
use Panda\Contracts\Database\DatabaseAdapter;

/**
 * Database Connection Interface
 *
 * @package Panda\Database
 * @version 0.1
 */
class Connection
{
    /**
     * @var DatabaseAdapter
     */
    protected $adapter;

    /**
     * The transaction error.
     *
     * @var string
     */
    protected $error;

    /**
     * Create a new database connection instance.
     *
     * @param DatabaseAdapter $adapter The database connection adapter.
     */
    public function __construct(DatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
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
            $value = $this->adapter->escape($value);

            // Replace escaped value
            $query = str_replace('{' . $key . '}', $value, $query);
        }

        try {
            // Execute query to handler
            return $this->adapter->query($query, $commit);
        } catch (Exception $ex) {
            // Store error message
            $this->error = $ex->getMessage();

            // Re-throw exception
            throw $ex;
        }
    }

    /**
     * @return DatabaseAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param DatabaseAdapter $adapter
     */
    public function setAdapter(DatabaseAdapter $adapter)
    {
        $this->adapter = $adapter;
    }
}
