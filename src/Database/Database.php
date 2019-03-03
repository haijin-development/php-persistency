<?php

namespace Haijin\Persistency\Database;

use Haijin\Instantiator\Global_Factory;
use  Haijin\Instantiator\Create;
use Haijin\Persistency\Types_Converters\Types_Converter;
use Haijin\Persistency\Statement_Compiler\Compiler;
use Haijin\Persistency\Errors\Connections\Database_Query_Error;
use Haijin\Persistency\Errors\Connections\Uninitialized_Connection_Error;
use Haijin\Persistency\Errors\Connections\Connection_Failure_Error;
use Haijin\Persistency\Errors\Connections\Named_Parameter_Not_Found_Error;

/**
 * Base class for database engines wrappers.
 * Databases objects are the public APIs to persistency engines like Postgres, Sphinx
 * or Mysql.
 * It has methods to store, update, delete and retrieve objects from the database.
 * Each database engine implements its own Database subclass.
 */
abstract class Database
{
    /**
     * The handle to an open connection to a Postgresql server.
     */
    protected $connection_handle;

    protected $types_converter;

    protected $query_inspector_closure;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct()
    {
        $this->connection_handle = null;
        $this->types_converter = $this->default_types_converter();
        $this->query_inspector_closure = null;
    }

    /// Type convertions

    public function default_types_converter()
    {
        return Create::a( Types_Converter::class )->with();
    }

    public function get_types_converter()
    {
        return $this->types_converter;
    }


    /// Connecting

    /**
     * Connects to the database.
     * Each database may have its own connection parameters, this method does not constrain
     * them.
     * Raises a Connection_Failure_Error is the connection fails.
     *
     * @param array $params A variable number of parameters required to connect to a
     *      particular database server.
     */
    abstract public function connect(...$params);

    /// Querying

    /**
     * Compiles the $query_closure and counts the number of matching records.
     * Returns the number of records.
     */
    abstract public function count($query_closure, $named_parameters = [], $binding = null);

    /**
     * Compiles the $query_closure and executes the compiled query in the server.
     * Returns the rows returned by the query execution.
     */
    public function query($query_closure, $named_parameters = [], $binding = null)
    {
        $compiled_statement =
            $this->compile( function($compiler) use($query_closure, $binding) {

                $compiler->query( function($query) use($query_closure, $binding) {

                    $query->eval( $query_closure, $binding );

                });

            });

        return $this->execute( $compiled_statement, $named_parameters );
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
    public function create($create_closure, $named_parameters = [], $binding = null)
    {
        $compiled_statement =
            $this->compile( function($compiler) use($create_closure, $binding) {

                $compiler->create( function($query) use($create_closure, $binding) {

                    $query->eval( $create_closure, $binding );

                });

            });

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $update_closure and executes the update record query in the database server.
     *
     * @param closure $update_closure A closure to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the update_closure.
     */
    public function update($update_closure, $named_parameters = [], $binding = null)
    {
        $compiled_statement =
            $this->compile( function($compiler) use($update_closure, $binding) {

                $compiler->update( function($query) use($update_closure, $binding) {

                    $query->eval( $update_closure, $binding );

                });

            });

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $delete_closure and executes the delete statement in the database server.
     *
     * @param closure $delete_closure A closure to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the delete_closure.
     */
    public function delete($delete_closure, $named_parameters = [], $binding = null)
    {
        $compiled_statement =
            $this->compile( function($compiler) use($delete_closure, $binding) {

                $compiler->delete( function($query) use($delete_closure, $binding) {

                    $query->eval( $delete_closure, $binding );

                });

            });

        return $this->execute( $compiled_statement, $named_parameters );
    }

    abstract public function get_last_created_id();

    abstract public function clear_all($collection_name);

    /// Compiling statements

    public function compile($closure, $binding = null)
    {
        return Global_Factory::with_factory_do( function($factory)
                                        use($closure, $binding) {

            $this->set_instantiators_during_compilation( $factory );

            return $this->new_compiler()->compile( $closure, $binding );

        }, $this );
    }

    protected function set_instantiators_during_compilation($factory)
    {
    }

    /// Executing statements

    /**
     * Executes the $query_statement.
     * Returns the result of the execution.
     */
    abstract public function execute_query_statement($query_statement, $named_parameters);

    /**
     * Executes the $create_statement.
     */
    abstract public function execute_create_statement($create_statement, $named_parameters);

    /**
     * Executes the $update_statement.
     */
    abstract public function execute_update_statement($update_statement, $named_parameters);

    /**
     * Executes the $delete_statement.
     */
    abstract public function execute_delete_statement($delete_statement, $named_parameters);

    public function during_transaction_do($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $commit = true;
        $this->begin_transaction();

        try {

            $closure->call( $binding, $this );

        } catch( \Exception $e ) {

            $this->rollback_transaction();

            $commit = false;

            throw $e;
            
        } finally {

            if( $commit ) {
                $this->commit_transaction();
            }

        }
    }

    abstract public function begin_transaction();
    abstract public function commit_transaction();
    abstract public function rollback_transaction();

    /**
     * Executes the $compiled_statement with the database server.
     * Returns the result of the execution.
     *
     * @param Haijin\Persistency\Statement_Compiler\Expression $compiled_statement A statement.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the statement.
     *
     * @return array An associative array with the results of the query execution.
     */
    public function execute($compiled_statement, $named_parameters = [])
    {
        $this->validate_connection_handle();

        return Global_Factory::with_factory_do( function($factory)
                                        use($compiled_statement, $named_parameters) {

            $this->set_instantiators_during_execution( $factory );

            return $compiled_statement->execute_in( $this, $named_parameters );

        }, $this );

    }

    protected function set_instantiators_during_execution($factory)
    {

    }

    /// Debugging

    /**
     * Evaluates the $inspector_closure with the debugging information about the
     * built query.
     * Each Database subclass defines the $inspector_closure parameters. For instance,
     * for sql database one parameter can be the built sql string.
     * This method is intenteded for debugging purposes, not to use in production.
     *
     * @param closure $closure A closure with the debugging information as its parametets.
     *
     * Example of use:
     *
     *      $database->inspect_query_with( function($sql, $query_parameters) {
     *          var_dump( $sql );
     *          var_dump( $query_parameters );
     *      });
     *
     *      $database->query( function($query) use($database) {
     *
     *          $query->collection( "users" );
     *
     *      });
     *
     *      $database->inspect_query_with( null );
     */
    public function inspect_query_with($closure)
    {
        $this->query_inspector_closure = $closure;
    }

    /// Creating instances

    protected function new_compiler()
    {
        return Create::a( Compiler::class )->with();
    }

    /// Validating

    /**
     * Validates that the connection_handle to the Mysql server was initialized.
     */
    protected function validate_connection_handle()
    {
        if( $this->connection_handle === null ) {
            $this->raise_uninitialized_connection_error();
        }
    }

    /// Raising errors

    /**
     * Raises a Connection_Failure_Error.
     *
     * @oaram string $error_message Optional - The error message of the Connection_Failure_Error.
     */
    protected function raise_connection_failed_error($error_message)
    {
        if( $error_message === null ) {
            $error_message = 'The connection to the database failed.';
        }

        throw Create::a( Connection_Failure_Error::class )->with( $error_message, $this );
    }

    /**
     * Raises a Database_Query_Error.
     *
     * @oaram string $error_message The error message of the Database_Query_Error.
     */
    protected function raise_database_query_error($error_message)
    {
        throw Create::a( Database_Query_Error::class )->with( $error_message, $this );
    }

    /**
     * Raises a Uninitialized_Connection_Error.
     */
    protected function raise_uninitialized_connection_error($error_message = null)
    {
        if( $error_message === null ) {
            $error_message = 'The connection handle has not being initialized. Initialize it with \'->connect($hostname, $user, $password, $database)\' first.';
        }

        throw Create::a( Uninitialized_Connection_Error::class )->with( $error_message, $this );
    }

    protected function raise_named_parameter_not_found_error($parameter_name)
    {
        throw Create::a( Named_Parameter_Not_Found_Error::class )->with(
            "The query named parameter '{$parameter_name}' was not found.",
            $parameter_name
        );
    }
}