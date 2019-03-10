<?php

namespace Haijin\Persistency\Engines\Sqlite;

use Haijin\Persistency\Database\Sql_Database;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Engines\Sqlite\Query_Builder\Sqlite_Expression_In_Filter_Builder;
use Haijin\Persistency\Engines\Sqlite\Query_Builder\Sqlite_Pagination_Builder;
use Haijin\Persistency\Types_Converters\Boolean_To_Integer;

class Sqlite_Database extends Sql_Database
{
    /// Types converters

    public function default_types_converter()
    {
        return parent::default_types_converter()->define( function($types_converter) {

            $types_converter->set_type_converter( "boolean", new Boolean_To_Integer() );

        });
    }

    /// Connecting

    /**
     * Connects to the Sqlite database.
     *
     * Parameters are:
     *      $params[0]      $filename  
     */
    public function connect(...$params)
    {
        try {

            $this->connection_handle = new \SQLite3( ...$params );

        } catch( \Exception $e ) {

            $this->connection_handle = null;

            $this->raise_connection_failed_error( $e->getMessage() );

        }

        return $this;
    }

    /// Transactions

    public function begin_transaction()
    {
        $result = $this->connection_handle->query( "begin;" );

        if( $result === false ) {
            $this->raise_database_query_error( $this->connection_handle->lastErrorMsg() );
        }
    }

    public function commit_transaction()
    {
        $result = $this->connection_handle->query( "commit;" );

        if( $result === false ) {
            $this->raise_database_query_error( $this->connection_handle->lastErrorMsg() );
        }
    }

    public function rollback_transaction()
    {
        $result = $this->connection_handle->query( "rollback;" );

        if( $result === false ) {
            $this->raise_database_query_error( $this->connection_handle->lastErrorMsg() );
        }
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

        return $result[ 0 ][ 'count(*)' ];
    }

    public function clear_all($collection_name)
    {
        $this->evaluate_sql_string( "delete from {$collection_name};" );
    }

    public function get_last_created_id()
    {
        $statement_handle = $this->connection_handle->prepare( "select last_insert_rowid() as id;" );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->lastErrorMsg() );
        }

        $result_handle = $statement_handle->execute();

        if( $result_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->lastErrorMsg() );
        }

        return $result_handle->fetchArray( SQLITE3_ASSOC )[ "id" ];
    }

    /// Executing

    protected function set_instantiators_during_execution($factory)
    {
        $factory->set(
            Sql_Pagination_Builder::class,
            Sqlite_Pagination_Builder::class
        );

        $factory->set(
            Sql_Expression_In_Filter_Builder::class,
            Sqlite_Expression_In_Filter_Builder::class
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

        $result_handle = $this->evaluate_sql_string( $sql, $sql_parameters );

        $rows = [];

        while( $row = $result_handle->fetchArray( SQLITE3_ASSOC ) ) {
            $rows[] = $row;
        }

        return $rows;

        return $this->process_result_rows( $result_rows );
    }

    /**
     * Ecaluates the $sql string as it is.
     * Returns nothing.
     */
    public function evaluate_sql_string($sql, $sql_parameters = [])
    {
        $statement_handle = $this->connection_handle->prepare( $sql );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->lastErrorMsg() );
        }

        $this->bind_parameters_to_statement( $statement_handle, $sql_parameters );

        $result_handle = $statement_handle->execute();

        if( $result_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->lastErrorMsg() );
        }

        return $result_handle;
    }

    /**
     * Binds the parameters to the Sqlite prepared statement.
     * Positional parameters are 1-based in Sqlite.
     */
    protected function bind_parameters_to_statement($statement_handle, $sql_parameters)
    {
        $statement_handle->reset();

        if( empty( $sql_parameters ) ) {
            return;
        }

        $types_converter = $this->get_types_converter();

        foreach( $sql_parameters as $i => $value ) {
            $value = $types_converter->convert_to_database( $value );

            $statement_handle->bindValue( $i + 1, $value );
        }
    }

    /// Double disptach

    public function visit($visitor)
    {
        return $visitor->accept_sqlite_database( $this );
    }
}