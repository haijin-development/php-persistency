<?php

namespace Haijin\Persistency\Mysql;

use Haijin\Persistency\Factory\Factory;
use Haijin\Persistency\Errors\Connections\NamedParameterNotFoundError;
use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;
use Haijin\Persistency\Sql\QueryBuilder\SqlPaginationBuilder;
use Haijin\Persistency\Sql\QueryBuilder\SqlExpressionInFilterBuilder;
use Haijin\Persistency\Mysql\QueryBuilder\MysqlPaginationBuilder;
use Haijin\Persistency\Mysql\QueryBuilder\MysqlExpressionInFilterBuilder;
use Haijin\Persistency\Mysql\QueryBuilder\NamedParameterPlaceholder;
use Haijin\Persistency\QueryBuilder\Builders\QueryExpressionBuilder;
use Haijin\Tools\Dictionary;
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
        try {
            $this->connection_handle = new \mysqli(
                $params[0],
                $params[1],
                $params[2],
                $params[3]
            );
        } catch(\Exception $e) {
            $this->raise_connection_failed_error( $e->getMessage() );
        }
    }

    /// Querying

    /**
     * Compiles the $query_closure and executes the compiled query in the server.
     * Returns the rows returned by the query execution. 
     */
    public function query($query_closure, $named_parameters = [])
    {
        $query_parameters = new OrderedCollection();
        $named_parameters = Dictionary::with_all( $named_parameters );

        $compiled_query = $this->compile_query( $query_closure, $query_parameters );

        return $this->execute( $compiled_query, $named_parameters, $query_parameters );
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
    public function execute($compiled_query, $named_parameters = [], $query_parameters = [])
    {
        $this->validate_connection_handle();

        $query_parameters = new OrderedCollection();

        $statement_handle = $this->_prepare_statement( $compiled_query, $query_parameters );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }

        $result_rows = $this->_execute_statement(
            $statement_handle,
            $named_parameters,
            $query_parameters
        );

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
    protected function _execute_statement($statement_handle, $named_parameters, $query_parameters)
    {
        $this->_bind_parameters_to_statement(
            $statement_handle,
            $named_parameters,
            $query_parameters
        );

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
    protected function _bind_parameters_to_statement($statement_handle, $named_parameters, $query_parameters)
    {
        if( $query_parameters->is_empty() ) {
            return;
        }

        $parameters_array = $query_parameters->to_array();

        $types = "";
        foreach( $parameters_array as $i => $value ) {

            if( method_exists( $value, "get_parameter_name" ) ) {
                $value = $named_parameters->at_if_absent(
                    $value->get_parameter_name(), function() use($value) {
                        $this->raise_named_parameter_not_found_error(
                            $value->get_parameter_name()
                        );
                }, $this );

                $parameters_array[ $i ] = $value;
            }

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
        return Factory::with_classes_do( function($factory)
                                    use($compiled_query, $query_parameters) {

            $factory->at_put( SqlPaginationBuilder::class, MysqlPaginationBuilder::class );

            $factory->at_put(
                SqlExpressionInFilterBuilder::class,
                function() use($query_parameters) {
                    return new MysqlExpressionInFilterBuilder( $query_parameters );
                }
            );

            return $this->new_sql_builder( $query_parameters )
                ->build_sql_from( $compiled_query );

        }, $this);
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

    /// Raising errors

    protected function raise_named_parameter_not_found_error($parameter_name)
    {
        throw new NamedParameterNotFoundError(
            "The query named parameter '{$parameter_name}' was not found.",
            $parameter_name
        );
    }

    /// Creating instances

    protected function new_query_expression_builder()
    {
        return new QueryExpressionBuilder();
    }

    protected function new_sql_builder()
    {
        return new SqlBuilder;
    }

    /// Debugging

    public function inspect_query($query_expression_builder, $closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $query_parameters = new OrderedCollection();

        $sql = $this->query_to_sql(
            $query_expression_builder->get_query_expression(),
            $query_parameters
        );

        return $closure->call( $binding, $sql, $query_parameters );
    }
}