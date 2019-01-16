<?php

namespace Haijin\Persistency\Errors\Connections;

use Haijin\Persistency\Errors\Persistency_Error;

/**
 * Error raised when the connection to a database server fails.
 */
class Connection_Failure_Error extends Persistency_Error
{
    /**
     * The database that failed to connect to the server.
     */
    protected $database;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param string $error_message The error message.
     * @param Database $database The database that failed to connect to the server.
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
     * @return Database The database that failed to connect to the server.
     */
    public function get_database()
    {
        return $this->database;
    }
}