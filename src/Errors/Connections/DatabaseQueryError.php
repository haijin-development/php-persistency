<?php

namespace Haijin\Persistency\Errors\Connections;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when the execution of a query in the database server fails.
 */
class DatabaseQueryError extends PersistencyError
{
    /**
     * The database where the error was raised.
     */
    protected $database;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param string $error_message The error message.
     * @param Database $database The database where the error was raised.
     */
    public function __construct($error_message, $database)
    {
        parent::__construct( $error_message );

        $this->database = $database;
    }

    /// Accessing

    /**
     * Returns the database that failed to connect to the server.
     *
     * @return Database The database where the error was raised.
     */
    public function get_database()
    {
        return $this->database;
    }
}