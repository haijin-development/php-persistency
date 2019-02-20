<?php

namespace Haijin\Persistency\Engines\Sqlite;

use Haijin\Instantiator\Global_Factory;
use Haijin\Instantiator\Create;
use Haijin\Dictionary;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error;
use Haijin\Persistency\Database\Database;
use Haijin\Persistency\Sql\Sql_Query_Statement_Builder;
use Haijin\Persistency\Sql\Sql_Pagination_Builder;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_In_Filter_Builder;
use Haijin\Persistency\Statement_Compiler\Query_Statement_Compiler;
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
        return $this->new_query_statement_compiler()
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
     * Executes the $compiled_query.
     * Returns the result of the execution.
     */
    public function execute_query_statement($compiled_query, $named_parameters)
    {
        $query_parameters = Create::an( Ordered_Collection::class )->with();

        $statement_handle = $this->prepare_statement( $compiled_query, $query_parameters );

        if( $statement_handle === false ) {
            $this->raise_database_query_error( $this->connection_handle->lastErrorMsg() );
        }

        $result_rows = $this->execute_statement(
            $statement_handle,
            $named_parameters,
            $query_parameters
        );

        return $this->process_result_rows( $result_rows );
    }

    /**
     * Creates and returns a rrepared Sqlite statement from a Query_Statement.
     */
    protected function prepare_statement($compiled_query, $query_parameters)
    {
        $sql = $this->query_to_sql( $compiled_query, $query_parameters );

        return $this->connection_handle->prepare( $sql );
    }

    /**
     * Binds the parameters to the Sqlite prepared statement and executes it.
     * Returns an associative array with the results.
     */
    protected function execute_statement($statement_handle, $named_parameters, $query_parameters)
    {
        $this->bind_parameters_to_statement(
            $statement_handle,
            $named_parameters,
            $query_parameters
        );

        $result_handle = $statement_handle->execute();

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
    protected function bind_parameters_to_statement($statement_handle, $named_parameters, $query_parameters)
    {
        $statement_handle->reset();

        if( $query_parameters->is_empty() ) {
            return;
        }

        $parameters_array = $query_parameters->to_array();

        $type = null;
        foreach( $parameters_array as $i => $value ) {

            if( method_exists( $value, "get_parameter_name" ) ) {

                $value = $named_parameters->at_if_absent(

                    $value->get_parameter_name(), function() use($value) {
                        $this->raise_named_parameter_not_found_error(
                            $value->get_parameter_name()
                        );

                }, $this );

            }

            if( is_string( $value ) )
                $type .= SQLITE3_TEXT;
            elseif( is_double( $value ) )
                $type .= SQLITE3_FLOAT;
            elseif( is_int( $value ) )
                $type .= SQLITE3_INTEGER;
            elseif( $value === null )
                $type .= SQLITE3_NULL;
            else
                $type;

            $statement_handle->bindValue( $i + 1, $value );

        }

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

    /**
     * Builds the SQL string from the given $compiled_query.
     */
    protected function query_to_sql($compiled_query, $query_parameters)
    {
        return Global_Factory::with_factory_do( function($factory)
                                    use($compiled_query, $query_parameters) {

            $factory->set( Sql_Pagination_Builder::class, Sqlite_Pagination_Builder::class );

            $factory->set(
                Sql_Expression_In_Filter_Builder::class,
                function() use($query_parameters) {
                    return Create::a( Sqlite_Expression_In_Filter_Builder::class )
                        ->with( $query_parameters );
                }
            );

            return $this->new_sql_builder( $query_parameters )
                ->build_sql_from( $compiled_query );

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

    protected function new_sql_builder()
    {
        return Create::a( Sql_Query_Statement_Builder::class )->with();
    }

    /// Debugging

    public function inspect_query($query_statement_compiler, $closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $query_parameters = Create::a( Ordered_Collection::class )->with();

        $sql = $this->query_to_sql(
            $query_statement_compiler->get_query_statement(),
            $query_parameters
        );

        return $closure->call( $binding, $sql, $query_parameters );
    }
}