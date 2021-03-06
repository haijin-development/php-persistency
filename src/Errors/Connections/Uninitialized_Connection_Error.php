<?php

namespace Haijin\Persistency\Errors\Connections;

use Haijin\Persistency\Errors\Persistency_Error;

/**
 * Error raised when the database is used before connecting to it.
 */
class Uninitialized_Connection_Error extends Persistency_Error
{
    /**
     * The database where the error was raised.
     */
    protected $database;

    /// Initializing

    public function __construct($error_message, $database)
    {
        parent::__construct( $error_message );

        $this->database = $database;
    }

    /// Accessing

    /**
     * Returns the database that failed to connect to the server.
     *
     * @return Database The database that failed to connect to the server.
     */
    public function get_database()
    {
        return $this->database;
    }
}