<?php

namespace Haijin\Persistency\Database;

use Haijin\Persistency\Errors\Connections\DatabaseQueryError;
use Haijin\Persistency\Errors\Connections\UninitializedConnectionError;
use Haijin\Persistency\Errors\Connections\ConnectionFailedError;
use Haijin\Persistency\QueryBuilder\Builders\QueryExpressionBuilder;

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
    public function query($query_closure)
    {
        return $this->execute(
            $this->compile_query( $query_closure )
        );
    }

    /**
     * Compiles the $query_closure and executes the compiled query in the server.
     * Returns the rows returned by the query execution. 
     */
    public function compile_query($query_closure)
    {
        return $this->new_query_expression_builder()
            ->build( $query_closure );
    }

    /// Executing

    /**
     * Executes the $compiled_query.
     * Returns the result of the execution.
     */
    abstract public function execute($compiled_query);

    /// Creating instances

    /**
     * Creates and returns a new QueryExpressionBuilder.
     */
    protected function new_query_expression_builder()
    {
        return new QueryExpressionBuilder();
    }

    /**
     * Creates and returns a new MysqlQueryBuilder.
     */
    abstract protected function new_mysql_query_builder();

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