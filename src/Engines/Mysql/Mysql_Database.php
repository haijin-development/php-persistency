<?php

namespace Haijin\Persistency\Engines\Mysql;

use Haijin\Instantiator\Global_Factory;
use  Haijin\Instantiator\Create;
use Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error;
use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Sql\Query_Builder\Sql_Query_Statement_Builder;
use Haijin\Persistency\Sql\Query_Builder\Sql_Create_Statement_Builder;
use Haijin\Persistency\Sql\Query_Builder\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Query_Builder\Expression_Builders\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Engines\Mysql\Query_Builder\Mysql_Pagination_Builder;
use Haijin\Persistency\Engines\Mysql\Query_Builder\Mysql_Expression_In_Filter_Builder;
use Haijin\Persistency\Query_Builder\Builders\Query_Statement_Builder;
use Haijin\Persistency\Query_Builder\Builders\Create_Statement_Builder;
use Haijin\Dictionary;
use Haijin\Ordered_Collection;


class Mysql_Database extends Database
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
        $this->connection_handle = new \mysqli( ...$params );

        if( $this->connection_handle->connect_errno ) {
            $error_message = $this->connection_handle->connect_error;

            $this->connection_handle = null;

            $this->raise_connection_failed_error( $error_message );
        }
    }

    /// Querying

    /**
     * Compiles the $query_closure and executes the compiled query in the server.
     * Returns the rows returned by the query execution.
     */
    public function query($query_closure, $named_parameters = [])
    {
        $named_parameters = Dictionary::with_all( $named_parameters );

        $compiled_query = $this->compile_query_statement( $query_closure );

        return $this->execute( $compiled_query, $named_parameters );
    }

    /**
     * Compiles the $create_closure and executes the create record query in the database server.
     * Returns the id of the created query.
     *
     * @param closure $create_closure A closure to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the create_closure.
     *
     * @return object The unique id of the created record in the database expression.
     */
    public function create_one($create_closure, $named_parameters = [])
    {
        $named_parameters = Dictionary::with_all( $named_parameters );

        $compiled_statement = $this->compile_create_statement( $create_closure );

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $query_closure and executes the compiled query in the server.
     * Returns the rows returned by the query execution.
     */
    public function compile_query_statement($query_closure)
    {
        return $this->new_query_statement_builder()
            ->build( $query_closure );
    }

    /**
     * Compiles the $create_closure and executes the compiled statement in the server.
     * Returns the rows returned by the query execution.
     */
    public function compile_create_statement($create_closure)
    {
        return $this->new_create_expression_builder()
            ->build( $create_closure );
    }

    /// Executing

    /**
     * Executes the $statement.
     * Returns the result of the execution.
     */
    public function execute($statement, $named_parameters = [])
    {
        $this->validate_connection_handle();

        return $statement->execute_in( $this, $named_parameters );
    }

    /**
     * Executes the $query_statement.
     * Returns the result of the execution.
     */
    public function execute_query_statement($query_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->query_statement_to_sql( $query_statement, $query_parameters );

        $statement_handle = $this->connection_handle->prepare( $sql );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }

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

        $result_rows = $result_handle->fetch_all( MYSQLI_ASSOC );

        return $this->process_result_rows( $result_rows );
    }

    /**
     * Executes the $create_statement.
     * Returns the result of the execution.
     */
    public function execute_create_statement($create_statement, $named_parameters, $query_parameters = [])
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with( $query_parameters );

        $sql = $this->create_statement_to_sql( $create_statement, $query_parameters );

        $statement_handle = $this->connection_handle->prepare( $sql );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }

        $this->_bind_parameters_to_statement(
            $statement_handle,
            $named_parameters,
            $query_parameters
        );

        $result = $statement_handle->execute();

        if( $result === false ) {
            $this->raise_database_query_error( $statement_handle->error );
        }

        return $this->connection_handle->insert_id;
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
    protected function process_result_rows($result_rows)
    {
        return $result_rows;
    }

    /**
     * Builds the SQL string from the given $compiled_query.
     */
    protected function query_statement_to_sql($query_statement, $query_parameters)
    {
        return Global_Factory::with_factory_do( function($factory)
                                    use($query_statement, $query_parameters) {

            $factory->set( Sql_Pagination_Builder::class, Mysql_Pagination_Builder::class );

            $factory->set(
                Sql_Expression_In_Filter_Builder::class,
                function() use($query_parameters) {
                    return Create::a( Mysql_Expression_In_Filter_Builder::class )
                                ->with( $query_parameters );
                }
            );

            return $this->new_query_statement_sql_builder( $query_parameters )
                ->build_sql_from( $query_statement );

        }, $this);
    }

    /**
     * Builds the SQL string from the given $create_statement.
     */
    protected function create_statement_to_sql($create_statement, $query_parameters)
    {
        return Global_Factory::with_factory_do( function($factory)
                                    use($create_statement, $query_parameters) {

            $factory->set( Sql_Pagination_Builder::class, Mysql_Pagination_Builder::class );

            $factory->set(
                Sql_Expression_In_Filter_Builder::class,
                function() use($query_parameters) {
                    return Create::a( Mysql_Expression_In_Filter_Builder::class )
                                ->with( $query_parameters );
                }
            );

            return $this->new_create_statement_sql_builder( $query_parameters )
                ->build_sql_from( $create_statement );

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
        throw Create::a( Named_Parameter_Not_Found_Error::class )->with(
            "The query named parameter '{$parameter_name}' was not found.",
            $parameter_name
        );
    }

    /// Creating instances

    protected function new_query_statement_builder()
    {
        return Create::a( Query_Statement_Builder::class )->with();
    }

    protected function new_create_expression_builder()
    {
        return Create::a( Create_Statement_Builder::class )->with();
    }

    protected function new_query_statement_sql_builder()
    {
        return Create::a( Sql_Query_Statement_Builder::class )->with();
    }

    protected function new_create_statement_sql_builder()
    {
        return Create::a( Sql_Create_Statement_Builder::class )->with();
    }

    /// Debugging

    public function inspect_query($query_statement_builder, $closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->query_statement_to_sql(
            $query_statement_builder->get_query_statement(),
            $query_parameters
        );

        return $closure->call( $binding, $sql, $query_parameters );
    }
}