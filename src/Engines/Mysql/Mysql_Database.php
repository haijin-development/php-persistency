<?php

namespace Haijin\Persistency\Engines\Mysql;

use Haijin\Persistency\Database\Sql_Database;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Engines\Mysql\Query_Builder\Mysql_Pagination_Builder;
use Haijin\Persistency\Engines\Mysql\Query_Builder\Mysql_Expression_In_Filter_Builder;

class Mysql_Database extends Sql_Database
{
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

        return $this;
    }

    /// Transactions

    public function begin_transaction()
    {
        $result = $this->connection_handle->query( "begin;" );

        if( $result === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }
    }

    public function commit_transaction()
    {
        $result = $this->connection_handle->query( "commit;" );

        if( $result === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }
    }

    public function rollback_transaction()
    {
        $result = $this->connection_handle->query( "rollback;" );

        if( $result === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }
    }

    /// Querying

    public function clear_all($collection_name)
    {
        $this->evaluate_sql_string( "truncate {$collection_name};" );
    }

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

        return $result[ 0 ][ 'count(*)' ];
    }

    /// Executing

    protected function set_instantiators_during_execution($factory)
    {
        $factory->set(
            Sql_Pagination_Builder::class,
            Mysql_Pagination_Builder::class
        );

        $factory->set(
            Sql_Expression_In_Filter_Builder::class,
            Mysql_Expression_In_Filter_Builder::class
        );
    }

    /**
     * Executes the $sql string as it is.
     * Returns the result of the execution.
     */
    public function execute_sql_string($sql, $sql_parameters = [])
    {
        if( $this->query_inspector_callable !== null ) {
            ($this->query_inspector_callable)( $sql, $sql_parameters );
        }

        $statement_handle = $this->connection_handle->prepare( $sql );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }

        $this->bind_parameters_to_statement( $statement_handle, $sql_parameters );

        $result = $statement_handle->execute();

        if( $result === false ) {
            $this->raise_database_query_error( $statement_handle->error );
        }

        $result_handle = $statement_handle->get_result();

        $result_rows = $result_handle->fetch_all( MYSQLI_ASSOC );

        return $this->process_result_rows( $result_rows );
    }

    /**
     * Evaluates the $sql string as it is.
     * Returns nothing.
     */
    public function evaluate_sql_string($sql, $sql_parameters = [])
    {
        $statement_handle = $this->connection_handle->prepare( $sql );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->error );
        }

        $this->bind_parameters_to_statement( $statement_handle, $sql_parameters );

        $result = $statement_handle->execute();

        if( $result === false ) {
            $this->raise_database_query_error( $statement_handle->error );
        }
    }

    public function get_last_created_id()
    {
        return $this->connection_handle->insert_id;
    }

    /**
     * Binds the parameters to the Mysql prepared statement.
     */
    protected function bind_parameters_to_statement($statement_handle, $sql_parameters)
    {
        if( empty( $sql_parameters ) ) {
            return;
        }

        $types_converter = $this->get_types_converter();

        $types = "";

        foreach( $sql_parameters as $i => $value ) {

            if( is_string( $value ) ) {
                $types .= "s";
            }
            elseif( is_double( $value ) ) {
                $types .= "d";
            }
            elseif( is_int( $value ) ) {
                $types .= "i";
            }
            elseif( $value === null ) {
                $types .= "i";
            }
            else {
                $types .= "s";
                $sql_parameters[ $i ] = $types_converter->convert_to_database( $value );
            }

        }

        $statement_handle->bind_param( $types, ...$sql_parameters );
    }

    /// Double disptach

    public function visit($visitor)
    {
        return $visitor->accept_mysql_database( $this );
    }
}