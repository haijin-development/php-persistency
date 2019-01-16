<?php

namespace Haijin\Persistency\Errors\Connections;

use Haijin\Persistency\Errors\Persistency_Error;

/**
 * Error raised when the execution of a query in the database server fails.
 */
class Database_Query_Error extends Persistency_Error
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