<?php

namespace Haijin\Persistency\Engines\Postgresql;

use Haijin\Persistency\Database\Sql_Database;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Engines\Postgresql\Query_Builder\Postgresql_Pagination_Builder;
use Haijin\Persistency\Engines\Postgresql\Query_Builder\Postgresql_Expression_In_Filter_Builder;

class Postgresql_Database extends Sql_Database
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

        return $this;
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
     * Compiles the $query_callable and counts the number of matching records.
     * Returns the number of records.
     */
    public function count($query_callable, $named_parameters = [])
    {
        $compiler = $this->new_compiler();

        $compiled_statement = $compiler->compile(
                                function($compiler) use($query_callable) {

            $compiler->query( function($query) use($query_callable) {

                $query->eval( $query_callable );

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
     * Executes the $sql string as it is.
     * Returns the result of the execution.
     */
    public function _execute_sql_string($sql, $sql_parameters = [])
    {
        $result_handle = $this->_evaluate_sql_string( $sql, $sql_parameters );

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
    public function _evaluate_sql_string($sql, $sql_parameters = [])
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

    /// Double disptach

    public function visit($visitor)
    {
        return $visitor->accept_postgres_database( $this );
    }
}