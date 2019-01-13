<?php

namespace Haijin\Persistency\Database;

use Haijin\Persistency\Errors\Connections\Database_Query_Error;
use Haijin\Persistency\Errors\Connections\Uninitialized_Connection_Error;
use Haijin\Persistency\Errors\Connections\Connection_Failure_Error;

/**
 * Base class for database engines wrappers.
 * Databases objects are the public APIs to persistency engines like Postgres, Sphinx
 * or Mysql.
 * It has methods to store, update, delete and retrieve objects from the database.
 * Each database engine implements its own Database subclass.
 */
abstract class Database
{
    /// Initializing

    /**
     * Initializes $this instance.
     * Placeholder method.
     */
    public function __construct()
    {
    }

    /// Connecting

    /**
     * Connects to the database.
     * Each database may have its own connection parameters, this method does not constrain
     * them.
     * Raises a Connection_Failure_Error is the connection fails.
     *
     * @param array $params A variable number of parameters required to connect to a
     *      particular database server.
     */
    abstract public function connect(...$params);

    /// Querying

    /**
     * Compiles the $query_closure and executes the compiled query in the database server.
     * Returns the rows returned by the query execution.
     *
     * @param closure $query_closure A closure to construct the database query.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the query closure.
     *
     * @return array An associative array with the results of the query execution.
     */
    abstract public function query($query_closure, $named_parameters = []);

    /**
     * Compiles the $query_closure and retunrs the compiled
     *      Haijin\Persistency\Query_Builder\Query_Expression.
     *
     * @param closure $query_closure A closure to construct the database query.
     *
     * @return Haijin\Persistency\Query_Builder\Query_Expression The Query_Expression compiled from
     *      the $query_closure evaluation.
     */
    abstract public function compile_query($query_closure);

    /// Executing

    /**
     * Executes the $compiled_query with the database server.
     * Returns the result of the execution.
     *
     * @param Haijin\Persistency\Query_Builder\Query_Expression $compiled_query A Query_Expression.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the Query_Expression.
     *
     * @return array An associative array with the results of the query execution.
     */
    abstract public function execute($compiled_query, $named_parameters = []);

    /// Debugging

    /**
     * Evaluates the $closure with the debugging information about the built query.
     * Each Database subclass defines the $closure parameters. For instance, for
     * sql database one parameter can be the built sql string.
     * This method is intenteded for debugging purposes, not to use in production.
     *
     * @param $query Query_Expression_Builder The parameter of the query_closure.
     * @param closure $closure A closure with the debugging information as its parametets.
     * @param object $binding Optional - An optional object to bind to the evaluation of
     *      the $closure. If none is given the $closure is bound to $this object.
     *
     * Example of use:
     *
     *      $database->query( function($query) use($database) {
     *
     *      $query->collection( "users" );
     *
     *      $database->inspect_query( $query, function($sql, $query_parameters) {
     *          var_dump( $sql );
     *          var_dump( $query_parameters );
     *      });
     *  });
     *
     */
    abstract public function inspect_query($query, $closure, $binding = null);

    /// Raising errors

    /**
     * Raises a Connection_Failure_Error.
     *
     * @oaram string $error_message Optional - The error message of the Connection_Failure_Error.
     */
    protected function raise_connection_failed_error($error_message)
    {
        if( $error_message === null ) {
            $error_message = 'The connection to the database failed.';
        }

        throw new Connection_Failure_Error( $error_message, $this );
    }

    /**
     * Raises a Database_Query_Error.
     *
     * @oaram string $error_message The error message of the Database_Query_Error.
     */
    protected function raise_database_query_error($error_message)
    {
        throw new Database_Query_Error( $error_message, $this );
    }

    /**
     * Raises a Uninitialized_Connection_Error.
     */
    protected function raise_uninitialized_connection_error($error_message = null)
    {
        if( $error_message === null ) {
            $error_message = 'The connection handle has not being initialized. Initialize it with \'->connect($hostname, $user, $password, $database)\' first.';
        }

        throw new Uninitialized_Connection_Error( $error_message, $this );
    }
}