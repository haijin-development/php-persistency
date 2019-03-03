<?php

namespace Haijin\Persistency\Engines\Postgresql;

use Haijin\Instantiator\Create;
use Haijin\Dictionary;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Create_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Update_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Delete_Statement_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;
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

    /**
     * Compiles the $query_closure and counts the number of matching records.
     * Returns the number of records.
     */
    public function count($query_closure, $named_parameters = [], $binding = null)
    {
        $compiler = $this->new_compiler();

        $compiled_statement = $compiler->compile(
                                function($compiler) use($query_closure, $binding) {

            $compiler->query( function($query) use($query_closure, $binding) {

                $query->eval( $query_closure, $binding );

            });

        });

        if( $compiled_statement->get_proyection_expression()->is_empty() ) {

            $compiled_statement = $compiler->eval( function($compiler) {

                $compiler->query( function($query) {

                    $query->proyect(
                        $query->count()
                    );

                });

            });

        }

        $result = $this->execute( $compiled_statement, $named_parameters );

        return $result[ 0 ][ 'count' ];
    }

    public function clear_all($collection_name)
    {
        $this->evaluate_sql_string( "truncate {$collection_name} restart identity;" );
    }

    /// Executing

    protected function set_instantiators_during_execution($factory)
    {
        $factory->set(
            Sql_Pagination_Builder::class,
            Postgresql_Pagination_Builder::class
        );

        $factory->set(
            Sql_Expression_In_Filter_Builder::class,
            Postgresql_Expression_In_Filter_Builder::class
        );
    }

    /**
     * Executes the $query_statement.
     * Returns the result of the execution.
     */
    public function execute_query_statement($query_statement, $named_parameters)
    {
        $builder = $this->new_sql_query_statement_builder();

        $sql = $builder->build_sql_from( $query_statement );

        $query_parameters = $builder->get_collected_parameters();

        $sql_parameters =
            $this->collect_parameters_from( $named_parameters, $query_parameters );

        return $this->execute_sql_string( $sql, $sql_parameters );
    }

    /**
     * Executes the $create_statement.
     */
    public function execute_create_statement($create_statement, $named_parameters)
    {
        $builder = $this->new_sql_create_statement_builder();

        $sql = $builder->build_sql_from( $create_statement );

        $query_parameters = $builder->get_collected_parameters();

        $sql_parameters =
            $this->collect_parameters_from( $named_parameters, $query_parameters );

        $result_handle = $this->evaluate_sql_string( $sql, $sql_parameters );

        \pg_free_result( $result_handle );
    }

    /**
     * Executes the $update_statement.
     */
    public function execute_update_statement($update_statement, $named_parameters)
    {
        $builder = $this->new_sql_update_statement_builder();

        $sql = $builder->build_sql_from( $update_statement );

        $query_parameters = $builder->get_collected_parameters();

        $sql_parameters =
            $this->collect_parameters_from( $named_parameters, $query_parameters );

        $result_handle = $this->evaluate_sql_string( $sql, $sql_parameters );

        \pg_free_result( $result_handle );
    }

    /**
     * Executes the $delete_statement.
     */
    public function execute_delete_statement($delete_statement, $named_parameters)
    {
        $builder = $this->new_sql_delete_statement_builder();

        $sql = $builder->build_sql_from( $delete_statement );

        $query_parameters = $builder->get_collected_parameters();

        $sql_parameters =
            $this->collect_parameters_from( $named_parameters, $query_parameters );

        $result_handle = $this->evaluate_sql_string( $sql, $sql_parameters );

        \pg_free_result( $result_handle );
    }

    /**
     * Executes the $sql string as it is.
     * Returns the result of the execution.
     */
    public function execute_sql_string($sql, $sql_parameters = [])
    {
        if( $this->query_inspector_closure !== null ) {
            ($this->query_inspector_closure)( $sql, $sql_parameters );
        }

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
    protected function collect_parameters_from($named_parameters, $query_parameters)
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

        }, $this ) ->to_array();

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