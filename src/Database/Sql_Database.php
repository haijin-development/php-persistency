<?php

namespace Haijin\Persistency\Database;

use Haijin\Instantiator\Create;
use Haijin\Dictionary;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Create_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Update_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Delete_Statement_Builder;

abstract class Sql_Database extends Database
{
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

    /**
     * Executes the $query_statement.
     * Returns the result of the execution.
     */
    public function execute_query_statement($query_statement, $named_parameters)
    {
        $builder = $this->new_sql_query_statement_builder();

        $sql = $builder->build_sql_from( $query_statement );

        $query_parameters = $builder->get_collected_parameters();

        $sql_parameters = $this->collect_parameters_from(
                $named_parameters,
                $query_parameters
            );

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

                });

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
        return Create::object( Sql_Query_Statement_Builder::class);
    }

    protected function new_sql_create_statement_builder()
    {
        return Create::object( Sql_Create_Statement_Builder::class);
    }

    protected function new_sql_update_statement_builder()
    {
        return Create::object( Sql_Update_Statement_Builder::class);
    }

    protected function new_sql_delete_statement_builder()
    {
        return Create::object( Sql_Delete_Statement_Builder::class);
    }

    /// Debugging

    public function inspect_query($query_statement_compiler, $callable)
    {
        $query_parameters = new Ordered_Collection();

        $sql = $this->query_statement_to_sql(
            $query_statement_compiler->get_query_statement(),
            $query_parameters
        );

        return $callable( $sql, $query_parameters );
    }
}