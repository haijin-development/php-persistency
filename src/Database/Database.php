<?php

namespace Haijin\Persistency\Database;

use Haijin\Persistency\Errors\Connections\DatabaseQueryError;
use Haijin\Persistency\Errors\Connections\UninitializedConnectionError;
use Haijin\Persistency\Errors\Connections\ConnectionFailedError;

abstract class Database
{
    /**
     * Initializes $this instance.
     */
    public function __construct()
    {
    }

    /// Connecting

    /**
     * Connects to the Mysql database.
     */
    abstract public function connect(...$params);

    /// Querying

    /**
     * Compiles the $query_closure and executes the compiled query in the server.
     * Returns the rows returned by the query execution. 
     */
    abstract public function query($query_closure, $named_parameters = []);

    /**
     * Compiles the $query_closure.
     */
    abstract public function compile_query($query_closure);

    /// Executing

    /**
     * Executes the $compiled_query.
     * Returns the result of the execution.
     */
    abstract public function execute($compiled_query, $named_parameters = []);

    /// Raising errors

    /**
     * Raises a DatabaseQueryError.
     */
    protected function raise_database_query_error($error_message)
    {
        throw new DatabaseQueryError( $error_message, $this );
    }

    /**
     * Raises a UninitializedConnectionError.
     */
    protected function raise_uninitialized_connection_error()
    {
        throw new UninitializedConnectionError(
            'The connection handle has not being initialized. Initialize it with \'->connect($hostname, $user, $password, $database)\' first.',
            $this
        );
    }
}