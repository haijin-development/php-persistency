<?php

namespace Haijin\Persistency\Database;

use  Haijin\Instantiator\Create;
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
     * Compiles the $query_closure and executes the query statement in the database server.
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
     * Compiles the $create_closure and executes the create statement in the database server.
     * Returns the id of the created query.
     *
     * @param closure $create_closure A closure to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the create_closure.
     *
     * @return object The unique id of the created record in the database expression.
     */
    abstract public function create($create_closure, $named_parameters = []);

    /**
     * Compiles the $update_closure and executes the update statement in the database server.
     *
     * @param closure $update_closure A closure to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the update_closure.
     */
    abstract public function update($update_closure, $named_parameters = []);

    /**
     * Compiles the $delete_closure and executes the delete statement in the database server.
     *
     * @param closure $delete_closure A closure to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the delete_closure.
     */
    abstract public function delete($delete_closure, $named_parameters = []);

    /**
     * Compiles the $query_closure and retunrs the compiled
     *      Haijin\Persistency\Statement_Compiler\Query_Statement.
     *
     * @param closure $query_closure A closure to construct the database query.
     *
     * @return Haijin\Persistency\Statement_Compiler\Query_Statement The Query_Statement compiled from
     *      the $query_closure evaluation.
     */
    abstract public function compile_query_statement($query_closure);

    /// Executing

    public function during_transaction_do($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $commit = true;
        $this->begin_transaction();

        try {

            $closure->call( $binding, $this );

        } catch( \Exception $e ) {

            $this->rollback_transaction();

            $commit = false;

            throw $e;
            
        } finally {

            if( $commit ) {
                $this->commit_transaction();
            }

        }
    }

    abstract public function begin_transaction();
    abstract public function commit_transaction();
    abstract public function rollback_transaction();

    /**
     * Executes the $compiled_statement with the database server.
     * Returns the result of the execution.
     *
     * @param Haijin\Persistency\Statement_Compiler\Expression $compiled_statement A statement.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the statement.
     *
     * @return array An associative array with the results of the query execution.
     */
    abstract public function execute($compiled_statement, $named_parameters = []);

    /// Debugging

    /**
     * Evaluates the $closure with the debugging information about the built query.
     * Each Database subclass defines the $closure parameters. For instance, for
     * sql database one parameter can be the built sql string.
     * This method is intenteded for debugging purposes, not to use in production.
     *
     * @param $query Query_Statement_Compiler The parameter of the query_closure.
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

        throw Create::a( Connection_Failure_Error::class )->with( $error_message, $this );
    }

    /**
     * Raises a Database_Query_Error.
     *
     * @oaram string $error_message The error message of the Database_Query_Error.
     */
    protected function raise_database_query_error($error_message)
    {
        throw Create::a( Database_Query_Error::class )->with( $error_message, $this );
    }

    /**
     * Raises a Uninitialized_Connection_Error.
     */
    protected function raise_uninitialized_connection_error($error_message = null)
    {
        if( $error_message === null ) {
            $error_message = 'The connection handle has not being initialized. Initialize it with \'->connect($hostname, $user, $password, $database)\' first.';
        }

        throw Create::a( Uninitialized_Connection_Error::class )->with( $error_message, $this );
    }
}