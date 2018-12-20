<?php

namespace Haijin\Persistency\Mysql;

use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Mysql\QueryBuilder\MysqlQueryBuilder;
use Haijin\Persistency\QueryBuilder\Builders\QueryExpressionBuilder;
use Haijin\Tools\OrderedCollection;

class MysqlDatabase extends Database
{
    /**
     * The handle to an open connection to a Mysql server.
     */
    protected $connection_handle;

    /**
     * Initializes $this instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->connection_handle = null;
    }

    /// Connecting

    /**
     * Connects to the Mysql database.
     *
     * Parameters are:
     *      [ $hostname, $user, $password, $database ]
     *
     *  from http://php.net/manual/en/mysqli.quickstart.connections.php
     */
    public function connect(...$params)
    {
        $this->connection_handle = new \mysqli(
            $params[0],
            $params[1],
            $params[2],
            $params[3]
        );
    }

    /// Querying

    /**
     * Compiles the $query_closure and executes the compiled query in the server.
     * Returns the rows returned by the query execution. 
     */
    public function query($query_closure)
    {
        $value_parameters = new OrderedCollection();

        $compiled_query = $this->compile_query( $query_closure, $value_parameters );

        return $this->execute( $compiled_query, $value_parameters );
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
    public function execute($compiled_query, $parameters = [])
    {
        $this->validate_connection_handle();

        $query_parameters = new OrderedCollection();

        $statement_handle = $this->_prepare_statement( $compiled_query, $query_parameters );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }

        $result_rows = $this->_execute_statement( $statement_handle, $query_parameters );

        return $this->_process_result_rows( $result_rows );
    }

    /**
     * Creates and returns a rrepared Mysql statement from a QueryExpression.
     */
    protected function _prepare_statement($compiled_query, $query_parameters)
    {
        $sql = $this->query_to_sql( $compiled_query, $query_parameters );

        return $this->connection_handle->prepare( $sql );
    }

    /**
     * Binds the parameters to the Mysql prepared statement and executes it.
     * Returns an associative array with the results.
     */
    protected function _execute_statement($statement_handle, $query_parameters)
    {
        $this->_bind_parameters_to_statement( $statement_handle, $query_parameters );

        $result = $statement_handle->execute();

        $result_handle = $statement_handle->get_result();
        if( $result_handle === false ) {
            $this->raise_database_query_error( $statement_handle->error );
        }

        return $result_handle->fetch_all( MYSQLI_ASSOC );
    }

    /**
     * Binds the parameters to the Mysql prepared statement.
     */
    protected function _bind_parameters_to_statement($statement_handle, $query_parameters)
    {
        if( $query_parameters->is_empty() ) {
            return;
        }

        $parameters_array = $query_parameters->to_array();

        $types = "";
        foreach($parameters_array as $i => $value) {
            if( is_string( $value ) )
                $types .= "s";
            elseif( is_double( $value ) )
                $types .= "d";
            elseif( is_int( $value ) )
                $types .= "i";
            else
                $types;
        }

        $statement_handle->bind_param( $types, ...$parameters_array );
    }

    /**
     * Process the associative array resulting from a Mysql query.
     * This method can be hooked by subclasses to map the associative array into
     * something else.
     */
    protected function _process_result_rows($result_rows)
    {
        return $result_rows;
    }

    /**
     * Builds the SQL string from the given $compiled_query.
     */
    protected function query_to_sql($compiled_query, $query_parameters)
    {
        return $this->new_mysql_query_builder( $query_parameters )
            ->build_sql_from( $compiled_query );
    }

    /// Validating

    /**
     * Validates that the connection_handle to the Mysql server was initialized.
     */
    protected function validate_connection_handle()
    {
        if( $this->connection_handle === null ) {
            $this->raise_uninitialized_connection_error();
        }
    }

    /// Creating instances

    /**
     * Creates and returns a new MysqlQueryBuilder.
     */
    protected function new_mysql_query_builder($query_parameters)
    {
        return new MysqlQueryBuilder( $query_parameters );
    }

    /**
     * Creates and returns a new MysqlQueryBuilder.
     */
    protected function new_query_expression_builder()
    {
        return new QueryExpressionBuilder();
    }
}