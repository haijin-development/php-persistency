<?php

namespace Haijin\Persistency\Engines\Postgresql;

use Haijin\Instantiator\Global_Factory;
use  Haijin\Instantiator\Create;
use Haijin\Dictionary;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Create_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Update_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Delete_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Engines\Postgresql\Query_Builder\Postgresql_Pagination_Builder;
use Haijin\Persistency\Engines\Postgresql\Query_Builder\Postgresql_Expression_In_Filter_Builder;
class Postgresql_Database extends Database
{
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

    /// Transactions

    public function begin_transaction()
    {
        $result_handle = \pg_query( $this->connection_handle, "begin;" );

        if( $result_handle === false ) {
            $this->raise_database_query_error( \pg_last_error( $this->connection_handle ) );
        }

        \pg_free_result( $result_handle );
    }

    public function commit_transaction()
    {
        $result_handle = \pg_query( $this->connection_handle, "commit;" );

        if( $result_handle === false ) {
            $this->raise_database_query_error( \pg_last_error( $this->connection_handle ) );
        }

        \pg_free_result( $result_handle );
    }

    public function rollback_transaction()
    {
        $result_handle = \pg_query( $this->connection_handle, "rollback;" );

        if( $result_handle === false ) {
            $this->raise_database_query_error( \pg_last_error( $this->connection_handle ) );
        }

        \pg_free_result( $result_handle );
    }

    /// Querying

    public function clear_all($collection_name)
    {
        $this->evaluate_sql_string( "truncate {$collection_name} restart identity;" );
    }

    /// Executing

    /**
     * Binds the parameters to the Postgresql prepared statement and executes it.
     * Returns an associative array with the results.
     */
    public function execute_query_statement($query_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->while_building_sql_do($query_statement, $query_parameters,
                    function() use($query_statement, $query_parameters) {

                    return $this->new_sql_query_statement_builder( $query_parameters )
                                ->build_sql_from( $query_statement );

                });

        $sql_parameters = $this
            ->collect_query_parameters( $named_parameters, $query_parameters )
            ->to_array();

        return $this->execute_sql_string( $sql, $sql_parameters );
    }

    /**
     * Binds the parameters to the Postgresql prepared statement and executes it.
     */
    public function execute_create_statement($create_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->while_building_sql_do($create_statement, $query_parameters,
                    function() use($create_statement, $query_parameters) {

                    return $this->new_sql_create_statement_builder( $query_parameters )
                                ->build_sql_from( $create_statement );

                });

        $sql_parameters = $this
            ->collect_query_parameters( $named_parameters, $query_parameters )
            ->to_array();

        $result_handle = $this->evaluate_sql_string( $sql, $sql_parameters );

        \pg_free_result( $result_handle );
    }

    /**
     * Binds the parameters to the Postgresql prepared statement and executes it.
     */
    public function execute_update_statement($update_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->while_building_sql_do($update_statement, $query_parameters,
                    function() use($update_statement, $query_parameters) {

                    return $this->new_sql_update_statement_builder( $query_parameters )
                                ->build_sql_from( $update_statement );

                });

        $sql_parameters = $this
            ->collect_query_parameters( $named_parameters, $query_parameters )
            ->to_array();

        $result_handle = $this->evaluate_sql_string( $sql, $sql_parameters );

        \pg_free_result( $result_handle );
    }

    /**
     * Binds the parameters to the Postgresql prepared statement and executes it.
     */
    public function execute_delete_statement($delete_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->while_building_sql_do($delete_statement, $query_parameters,
                    function() use($delete_statement, $query_parameters) {

                    return $this->new_sql_delete_statement_builder( $query_parameters )
                                ->build_sql_from( $delete_statement );

                });

        $sql_parameters = $this
            ->collect_query_parameters( $named_parameters, $query_parameters )
            ->to_array();

        $result_handle = $this->evaluate_sql_string( $sql, $sql_parameters );

        \pg_free_result( $result_handle );
    }

    /**
     * Executes the $sql string as it is.
     * Returns the result of the execution.
     */
    public function execute_sql_string($sql, $sql_parameters = [])
    {
        $result_handle = $this->evaluate_sql_string( $sql, $sql_parameters );

        $rows = pg_fetch_all( $result_handle );

        \pg_free_result( $result_handle );

        if( $rows === false ) {
            $rows = [];
        }

        return $this->process_result_rows( $rows );
    }

    /**
     * Evaluates the $sql string as it is.
     * Returns the result handle the evaluation. The result handle must be released with
     * .\pg_free_result( $result_handle );
     */
    public function evaluate_sql_string($sql, $sql_parameters = [])
    {
        $types_converter = $this->get_types_converter();

        foreach( $sql_parameters as $i => $value ) {

            if( $value === null ) {
                $value = "null";
            } elseif( $value === true ) {
                $value = "true";
            } elseif( $value === false ) {
                $value = "false";
            } elseif( is_int( $value ) || is_double( $value ) ) {
                $value = $value;
            } else {
                $value = $types_converter->convert_to_database( $value );
                $value = \pg_escape_literal( $this->connection_handle, $value );
            }

            $i += 1;

            $sql = preg_replace( "|\\$$i|", $value, $sql );

        }

        $result_handle = \pg_query( $this->connection_handle, $sql );

        if( $result_handle === false ) {
            $this->raise_database_query_error( \pg_last_error( $this->connection_handle ) );
        }

        return $result_handle;
    }

    public function get_last_created_id()
    {
        $result_handle = pg_query( $this->connection_handle, "select lastval();" );

        if( $result_handle === false ) {
            $this->raise_database_query_error( \pg_last_error( $this->connection_handle ) );
        }

        return pg_fetch_all( $result_handle )[0][ "lastval" ];
    }

    /**
     * Binds the parameters to the Postgresql prepared statement.
     */
    protected function collect_query_parameters($named_parameters, $query_parameters)
    {
        $named_parameters = Dictionary::with_all( $named_parameters );

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

    protected function while_building_sql_do($statement, $query_parameters, $closure)
    {
        return Global_Factory::with_factory_do( function($factory) use($closure, $query_parameters) {

            $factory->set( Sql_Pagination_Builder::class, Postgresql_Pagination_Builder::class );

            $factory->set(
                Sql_Expression_In_Filter_Builder::class,
                function() use($query_parameters) {
                    return Create::a( Postgresql_Expression_In_Filter_Builder::class )
                                ->with( $query_parameters );
                }
            );

            return $closure->call( $this );

        }, $this);
    }

    /// Raising errors

    /// Creating instances

    protected function new_sql_query_statement_builder()
    {
        return Create::a( Sql_Query_Statement_Builder::class )->with();
    }

    protected function new_sql_create_statement_builder()
    {
        return Create::a( Sql_Create_Statement_Builder::class )->with();
    }

    protected function new_sql_update_statement_builder()
    {
        return Create::a( Sql_Update_Statement_Builder::class )->with();
    }

    protected function new_sql_delete_statement_builder()
    {
        return Create::a( Sql_Delete_Statement_Builder::class )->with();
    }

    /// Debugging

    public function inspect_query($query_statement_compiler, $closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->query_to_sql(
            $query_statement_compiler->get_query_statement(),
            $query_parameters
        );

        return $closure->call( $binding, $sql, $query_parameters );
    }
}