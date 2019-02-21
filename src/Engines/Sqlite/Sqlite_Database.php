<?php

namespace Haijin\Persistency\Engines\Sqlite;

use Haijin\Instantiator\Global_Factory;
use Haijin\Instantiator\Create;
use Haijin\Dictionary;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error;
use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Create_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Update_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Delete_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;
use Haijin\Persistency\Statement_Compiler\Create_Statement_Compiler;
use Haijin\Persistency\Statement_Compiler\Update_Statement_Compiler;
use Haijin\Persistency\Statement_Compiler\Delete_Statement_Compiler;
use Haijin\Persistency\Engines\Sqlite\Query_Builder\Sqlite_Expression_In_Filter_Builder;
use Haijin\Persistency\Engines\Sqlite\Query_Builder\Sqlite_Pagination_Builder;


class Sqlite_Database extends Database
{
    /**
     * The handle to an open connection to a Sqlite server.
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
    public function create($create_closure, $named_parameters = [])
    {
        $named_parameters = Dictionary::with_all( $named_parameters );

        $compiled_statement = $this->compile_create_statement( $create_closure );

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $update_closure and executes the update record query in the database server.
     *
     * @param closure $update_closure A closure to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the update_closure.
     */
    public function update($update_closure, $named_parameters = [])
    {
        $named_parameters = Dictionary::with_all( $named_parameters );

        $compiled_statement = $this->compile_update_statement( $update_closure );

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $delete_closure and executes the delete statement in the database server.
     *
     * @param closure $delete_closure A closure to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the delete_closure.
     */
    public function delete($delete_closure, $named_parameters = [])
    {
        $named_parameters = Dictionary::with_all( $named_parameters );

        $compiled_statement = $this->compile_delete_statement( $delete_closure );

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $query_closure and returns the compiled Query_Statement.
     */
    public function compile_query_statement($query_closure)
    {
        return $this->new_query_statement_compiler()
            ->build( $query_closure );
    }

    /**
     * Compiles the $create_closure and returns the compiled Query_Statement.
     */
    public function compile_create_statement($create_closure)
    {
        return $this->new_create_statement_compiler()
            ->build( $create_closure );
    }

    /**
     * Compiles the $update_closure and returns the compiled Query_Statement.
     */
    public function compile_update_statement($update_closure)
    {
        return $this->new_update_statement_compiler()
            ->build( $update_closure );
    }

    /**
     * Compiles the $delete_closure and returns the compiled Query_Statement.
     */
    public function compile_delete_statement($delete_closure)
    {
        return $this->new_delete_statement_compiler()
            ->build( $delete_closure );
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

        $sql = $this->while_building_sql_do($query_statement, $query_parameters,
                    function() use($query_statement, $query_parameters) {

                    return $this->new_sql_query_statement_builder( $query_parameters )
                                ->build_sql_from( $query_statement );

                });

        $sql_parameters = $this->collect_parameters_from( $named_parameters, $query_parameters );

        return $this->execute_sql_string( $sql, $sql_parameters );
    }

    /**
     * Executes the $create_statement.
     * Returns the result of the execution.
     */
    public function execute_create_statement($create_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->while_building_sql_do($create_statement, $query_parameters,
                    function() use($create_statement, $query_parameters) {

                    return $this->new_sql_create_statement_builder( $query_parameters )
                                ->build_sql_from( $create_statement );

                });

        $sql_parameters = $this->collect_parameters_from( $named_parameters, $query_parameters );

        return $this->evaluate_sql_string($sql, $sql_parameters );
    }

    /**
     * Executes the $update_statement.
     * Returns the result of the execution.
     */
    public function execute_update_statement($update_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->while_building_sql_do($update_statement, $query_parameters,
                    function() use($update_statement, $query_parameters) {

                    return $this->new_sql_update_statement_builder( $query_parameters )
                                ->build_sql_from( $update_statement );

                });

        $sql_parameters = $this->collect_parameters_from( $named_parameters, $query_parameters );

        return $this->evaluate_sql_string($sql, $sql_parameters );
    }

    /**
     * Executes the $delete_statement.
     * Returns the result of the execution.
     */
    public function execute_delete_statement($delete_statement, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $sql = $this->while_building_sql_do($delete_statement, $query_parameters,
                    function() use($delete_statement, $query_parameters) {

                    return $this->new_sql_delete_statement_builder( $query_parameters )
                                ->build_sql_from( $delete_statement );

                });

        $sql_parameters = $this->collect_parameters_from( $named_parameters, $query_parameters );

        return $this->evaluate_sql_string($sql, $sql_parameters );
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

    /**
     * Executes the $sql string as it is.
     * Returns the result of the execution.
     */
    public function execute_sql_string($sql, $sql_parameters = [])
    {
        $result_rows = $this->evaluate_sql_string( $sql, $sql_parameters );

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

        $rows = [];

        while( $row = $result_handle->fetchArray( SQLITE3_ASSOC ) ) {
            $rows[] = $row;
        }

        return $rows;
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

        foreach( $sql_parameters as $i => $value ) {
            $statement_handle->bindValue( $i + 1, $value );
        }
    }

    protected function collect_parameters_from($named_parameters, $query_parameters)
    {
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
     * Process the associative array resulting from a Sqlite query.
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

            $factory->set( Sql_Pagination_Builder::class, Sqlite_Pagination_Builder::class );

            $factory->set(
                Sql_Expression_In_Filter_Builder::class,
                function() use($query_parameters) {
                    return Create::a( Sqlite_Expression_In_Filter_Builder::class )
                        ->with( $query_parameters );
                }
            );

            return $closure->call( $this );

        }, $this);
    }

    /// Validating

    /**
     * Validates that the connection_handle to the Sqlite server was initialized.
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

    protected function new_query_statement_compiler()
    {
        return Create::a( Query_Statement_Compiler::class )->with();
    }

    protected function new_create_statement_compiler()
    {
        return Create::a( Create_Statement_Compiler::class )->with();
    }

    protected function new_update_statement_compiler()
    {
        return Create::a( Update_Statement_Compiler::class )->with();
    }

    protected function new_delete_statement_compiler()
    {
        return Create::a( Delete_Statement_Compiler::class )->with();
    }

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

        $query_parameters = Create::a( Ordered_Collection::class )->with();

        $sql = $this->query_statement_to_sql(
            $query_statement_compiler->get_query_statement(),
            $query_parameters
        );

        return $closure->call( $binding, $sql, $query_parameters );
    }
}