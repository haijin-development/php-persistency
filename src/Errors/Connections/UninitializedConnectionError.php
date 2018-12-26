<?php

namespace Haijin\Persistency\Errors\Connections;

use Haijin\Persistency\Errors\PersistencyError;

/**
 * Error raised when the database is used before connecting to it.
 */
class UninitializedConnectionError extends PersistencyError
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