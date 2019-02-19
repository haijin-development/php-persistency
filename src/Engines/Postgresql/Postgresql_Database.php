<?php

namespace Haijin\Persistency\Engines\Postgresql;

use Haijin\Instantiator\Global_Factory;
use  Haijin\Instantiator\Create;
use Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error;
use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Sql\Query_Builder\Sql_Query_Statement_Builder;
use Haijin\Persistency\Sql\Query_Builder\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Query_Builder\Expression_Builders\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Engines\Postgresql\Query_Builder\Postgresql_Pagination_Builder;
use Haijin\Persistency\Engines\Postgresql\Query_Builder\Postgresql_Expression_In_Filter_Builder;
use Haijin\Persistency\Query_Builder\Builders\Query_Statement_Builder;
use Haijin\Dictionary;
use Haijin\Ordered_Collection;


class Postgresql_Database extends Database
{
    /**
     * The handle to an open connection to a Postgresql server.
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
     * Connects to the Postgresql database.
     *
     * Parameters are:
     *      params[0] $connect_string
     */
    public function connect(...$params)
    {
        $this->connection_handle = \pg_connect( ...$params );

        if( $this->connection_handle === false ) {

            $this->connection_handle = null;

            $this->raise_connection_failed_error( "Connection failed." );

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
     * Binds the parameters to the Postgresql prepared statement and executes it.
     * Returns an associative array with the results.
     */
    public function execute_query_statement($query_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->query_statement_to_sql( $query_statement, $query_parameters );

        $query_parameters = $this
            ->collect_query_parameters( $named_parameters, $query_parameters )
            ->to_array();

        foreach( $query_parameters as $i => $value ) {

            if( $value === null ) {
                $value = "null";
            } elseif( $value === true ) {
                $value = "true";
            } elseif( $value === false ) {
                $value = "false";
            } elseif( is_int( $value ) || is_double( $value ) ) {
                $value = $value;
            } else {
                $value = \pg_escape_literal( $this->connection_handle, $value );
            }

            $i += 1;

            $sql = preg_replace( "|\\$$i|", $value, $sql );

        }

        $result_handle = \pg_query( $this->connection_handle, $sql );

        if( $result_handle === false ) {
            $this->raise_database_query_error( \pg_last_error( $this->connection_handle ) );
        }

        $rows = pg_fetch_all( $result_handle );

        \pg_free_result( $result_handle );


        return $this->process_result_rows( $rows );
    }

    /**
     * Binds the parameters to the Postgresql prepared statement.
     */
    protected function collect_query_parameters($named_parameters, $query_parameters)
    {
        return $query_parameters->collect( function($value) use($named_parameters) {

            if( method_exists( $value, "get_parameter_name" ) ) {

                $value = $named_parameters->at_if_absent(

                    $value->get_parameter_name(), function() use($value) {

                        $this->raise_named_parameter_not_found_error(
                            $value->get_parameter_name()
                        );

                }, $this );

            }

            return $value;

        }, $this );

    }

    /**
     * Process the associative array resulting from a Postgresql query.
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
    protected function query_statement_to_sql($compiled_query, $query_parameters)
    {
        return Global_Factory::with_factory_do( function($factory)
                                    use($compiled_query, $query_parameters) {

            $factory->set( Sql_Pagination_Builder::class, Postgresql_Pagination_Builder::class );

            $factory->set(
                Sql_Expression_In_Filter_Builder::class,
                function() use($query_parameters) {
                    return Create::a( Postgresql_Expression_In_Filter_Builder::class )
                                ->with( $query_parameters );
                }
            );

            return $this->new_sql_query_statement_builder( $query_parameters )
                ->build_sql_from( $compiled_query );

        }, $this );

    }

    /// Validating

    /**
     * Validates that the connection_handle to the Postgresql server was initialized.
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

    protected function new_sql_query_statement_builder()
    {
        return Create::a( Sql_Query_Statement_Builder::class )->with();
    }

    /// Debugging

    public function inspect_query($query_statement_builder, $closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->query_to_sql(
            $query_statement_builder->get_query_statement(),
            $query_parameters
        );

        return $closure->call( $binding, $sql, $query_parameters );
    }
}