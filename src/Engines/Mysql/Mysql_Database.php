<?php

namespace Haijin\Persistency\Engines\Mysql;

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
use Haijin\Persistency\Engines\Mysql\Query_Builder\Mysql_Pagination_Builder;
use Haijin\Persistency\Engines\Mysql\Query_Builder\Mysql_Expression_In_Filter_Builder;

class Mysql_Database extends Database
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
     * Compiles the $query_closure and counts the number of matching records.
     * Returns the number of records.
     */
    public function count($query_closure, $named_parameters = [], $binding = null)
    {
        $compiler = $this->new_compiler();

        $compiled_statement = $compiler->compile(
                                function($compiler) use($query_closure) {

            $compiler->query( function($query) use($query_closure) {

                $query->eval( $query_closure, $this );

            }, $this );

        }, $binding );

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

        return $this->evaluate_sql_string( $sql, $sql_parameters );
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

        return $this->evaluate_sql_string( $sql, $sql_parameters );
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

        return $this->evaluate_sql_string( $sql, $sql_parameters );
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

    protected function collect_parameters_from($named_parameters, $query_parameters)
    {
        $named_parameters = Dictionary::with_all( $named_parameters );

        $sql_parameters = [];

        foreach( $query_parameters->to_array() as $i => $value ) {

            if( method_exists( $value, "get_parameter_name" ) ) {

                $value = $named_parameters->at_if_absent(

                    $value->get_parameter_name(), function() use($value) {
                        $this->raise_named_parameter_not_found_error(
                            $value->get_parameter_name()
                        );

                }, $this );

            }

            $sql_parameters[] = $value;

        }

        return $sql_parameters;
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

        $sql = $this->query_statement_to_sql(
            $query_statement_compiler->get_query_statement(),
            $query_parameters
        );

        return $closure->call( $binding, $sql, $query_parameters );
    }

    /// Double disptach

    public function visit($visitor)
    {
        return $visitor->accept_mysql_database( $this );
    }
}