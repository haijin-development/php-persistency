<?php

namespace Haijin\Persistency\Database;

use Haijin\Instantiator\Global_Factory;
use Haijin\Instantiator\Create;
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

    protected $query_inspector_callable;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct()
    {
        $this->connection_handle = null;
        $this->types_converter = $this->default_types_converter();
        $this->query_inspector_callable = null;
    }

    /// Type convertions

    public function default_types_converter()
    {
        return Create::object( Types_Converter::class);
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
     * Compiles the $query_callable and counts the number of matching records.
     * Returns the number of records.
     */
    abstract public function count($query_callable, $named_parameters = []);

    /**
     * Compiles the $query_callable and executes the compiled query in the server.
     * Returns the rows returned by the query execution.
     */
    public function query($query_callable, $named_parameters = [])
    {
        $compiled_statement =
            $this->compile( function($compiler) use($query_callable) {

                $compiler->query( function($query) use($query_callable) {

                    $query->eval( $query_callable );

                });

            });

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $create_callable and executes the create record query in the database server.
     * Returns the id of the created query.
     *
     * @param callable $create_callable A callable to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the create_callable.
     *
     * @return object The unique id of the created record in the database expression.
     */
    public function create($create_callable, $named_parameters = [])
    {
        $compiled_statement =
            $this->compile( function($compiler) use($create_callable) {

                $compiler->create( function($query) use($create_callable) {

                    $query->eval( $create_callable );

                });

            });

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $update_callable and executes the update record query in the database server.
     *
     * @param callable $update_callable A callable to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the update_callable.
     */
    public function update($update_callable, $named_parameters = [])
    {
        $compiled_statement =
            $this->compile( function($compiler) use($update_callable) {

                $compiler->update( function($query) use($update_callable) {

                    $query->eval( $update_callable );

                });

            });

        return $this->execute( $compiled_statement, $named_parameters );
    }

    /**
     * Compiles the $delete_callable and executes the delete statement in the database server.
     *
     * @param callable $delete_callable A callable to construct the record creation.
     * @param array $named_parameters An associative array of the named parameters values
     *      referenced in the delete_callable.
     */
    public function delete($delete_callable, $named_parameters = [])
    {
        $compiled_statement =
            $this->compile( function($compiler) use($delete_callable) {

                $compiler->delete( function($query) use($delete_callable ) {

                    $query->eval( $delete_callable );

                });

            });

        return $this->execute( $compiled_statement, $named_parameters );
    }

    abstract public function get_last_created_id();

    abstract public function clear_all($collection_name);

    /// Compiling statements

    public function compile($callable)
    {
        return Global_Factory::with_factory_do( function($factory) use($callable) {

            $this->set_instantiators_during_compilation( $factory );

            return $this->new_compiler()->compile( $callable );

        });
    }

    protected function set_instantiators_during_compilation($factory)
    {
    }

    /// Executing statements

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
        $this->validate_named_parameters( $named_parameters );

        $this->validate_connection_handle();

        return Global_Factory::with_factory_do( function($factory)
                                        use($compiled_statement, $named_parameters) {

            $this->set_instantiators_during_execution( $factory );

            return $compiled_statement->execute_in( $this, $named_parameters );

        });

    }

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

    public function during_transaction_do($callable)
    {
        $commit = true;
        $this->begin_transaction();

        try {

            $callable( $this );

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

    protected function set_instantiators_during_execution($factory)
    {

    }

    /// Debugging

    /**
     * Evaluates the $inspector_callable with the debugging information about the
     * built query.
     * Each Database subclass defines the $inspector_callable parameters. For instance,
     * for sql database one parameter can be the built sql string.
     * This method is intenteded for debugging purposes, not to use in production.
     *
     * @param callable $callable A callable with the debugging information as its parametets.
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
    public function inspect_query_with($callable)
    {
        $this->query_inspector_callable = $callable;
    }

    /// Creating instances

    protected function new_compiler()
    {
        return Create::object( Compiler::class);
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

    protected function validate_named_parameters($named_parameters)
    {
        if( ! is_array( $named_parameters ) ) {
            $this->raise_invalid_named_parameter_error();
        }
    }

    /// Double disptach

    abstract public function visit($visitor);

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

        throw new Connection_Failure_Error( $error_message, $this );
    }

    /**
     * Raises a Database_Query_Error.
     *
     * @oaram string $error_message The error message of the Database_Query_Error.
     */
    protected function raise_database_query_error($error_message)
    {
        throw new Database_Query_Error( $error_message, $this );
    }

    /**
     * Raises a Uninitialized_Connection_Error.
     */
    protected function raise_uninitialized_connection_error($error_message = null)
    {
        if( $error_message === null ) {
            $error_message = 'The connection handle has not being initialized. Initialize it with \'->connect($hostname, $user, $password, $database)\' first.';
        }

        throw new Uninitialized_Connection_Error( $error_message, $this );
    }

    protected function raise_named_parameter_not_found_error($parameter_name)
    {
        throw new Named_Parameter_Not_Found_Error(
            "The query named parameter '{$parameter_name}' was not found.",
            $parameter_name
        );
    }

    protected function raise_invalid_named_parameter_error()
    {
        throw new Haijin_Error(
            "Expected parameter to be an associative array"
        );
    }
}