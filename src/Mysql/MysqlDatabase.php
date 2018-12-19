<?php

namespace Haijin\Persistency\Mysql;

use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Mysql\QueryBuilder\MysqlQueryBuilder;

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

    /// Executing

    /**
     * Executes the $compiled_query.
     * Returns the result of the execution.
     */
    public function execute($compiled_query)
    {
        $this->validate_connection_handle();

        $statement_handle = $this->_prepare_statement($compiled_query);

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }

        $result_rows = $this->_execute_statement( $statement_handle );

        return $this->_process_result_rows( $result_rows );
    }

    /**
     * Creates and returns a rrepared Mysql statement from a QueryExpression.
     */
    protected function _prepare_statement($compiled_query)
    {
        $sql = $this->query_to_sql( $compiled_query );

        return $this->connection_handle->prepare( $sql );
    }

    /**
     * Binds the parameters to the Mysql prepared statement and executes it.
     * Returns an associative array with the results.
     */
    protected function _execute_statement($statement_handle, $params = [])
    {
        $this->_bind_parameters_to_statement( $statement_handle, $params );

        $statement_handle->execute();

        $result_handle = $statement_handle->get_result();

        return $result_handle->fetch_all( MYSQLI_ASSOC );
    }

    /**
     * Binds the parameters to the Mysql prepared statement.
     */
    protected function _bind_parameters_to_statement($statement_handle, $params = [])
    {
        // $statement_handle->bind_param();
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
    protected function query_to_sql($compiled_query)
    {
        return $this->new_mysql_query_builder()->build_sql_from( $compiled_query );
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
    protected function new_mysql_query_builder()
    {
        return new MysqlQueryBuilder();
    }
}