<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Instantiator\Global_Factory;
use Haijin\Instantiator\Create;
use Haijin\Errors\Haijin_Error;
use Haijin\Persistency\Database\Database;
use Elasticsearch\ClientBuilder;
use Haijin\Persistency\Types_Converters\Null_Converter;
use Haijin\Persistency\Engines\Named_Parameter_Placerholder;
use Haijin\Persistency\Engines\Elasticsearch\Statements_Compiler\Elasticsearch_Compiler;
use Haijin\Persistency\Engines\Elasticsearch\Types_Converters\Elasticsearch_DateTime_Converter;
use Haijin\Persistency\Engines\Elasticsearch\Query_Builder\Elasticsearch_Query_Builder;

use Haijin\Persistency\Statements\Update_Statement;
use Haijin\Persistency\Engines\Elasticsearch\Statements\Elasticsearch_Update_Statement;

use Haijin\Persistency\Statement_Compiler\Update_Statement_Compiler;
use Haijin\Persistency\Engines\Elasticsearch\Statements_Compiler\Elasticsearch_Update_Statement_Compiler;

use Haijin\Persistency\Announcements\About_To_Execute_Elasticsearch_Statement;

class Elasticsearch_Database extends Database
{
    protected $last_created_id;

    /**
     * Initializes $this instance.
     */
    public function __construct()
    {
        parent::__construct();

        $this->last_created_id = null;
    }

    /// Types converters

    public function default_types_converter()
    {
        return parent::default_types_converter()->define( function($types_converter) {

            $types_converter->set_type_converter( "boolean", new Null_Converter() );

            $types_converter->set_type_converter( "date",
                new Elasticsearch_DateTime_Converter()
            );

            $types_converter->set_type_converter(
                "time",
                new Elasticsearch_DateTime_Converter()
            );

            $types_converter->set_type_converter(
                "date_time",
                new Elasticsearch_DateTime_Converter()
            );

            $types_converter->set_type_converter(
                "json",
                new Null_Converter()
            );

        });
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
    public function connect(...$params)
    {
        $callable = $params[ 0 ];

        $this->connection_handle = ClientBuilder::create();

        $this->with_handle_do( $callable );

        $this->connection_handle = $this->connection_handle->build();

        return $this;
    }

    public function with_handle_do($callable)
    {
        return $callable( $this->connection_handle );
    }

    /// Transactions

    public function begin_transaction()
    {
    }

    public function commit_transaction()
    {
    }

    public function rollback_transaction()
    {
    }

    /// Compiling

    protected function set_instantiators_during_compilation($factory)
    {
        $factory->set(
            Update_Statement::class,
            Elasticsearch_Update_Statement::class
        );

        $factory->set(
            Update_Statement_Compiler::class,
            Elasticsearch_Update_Statement_Compiler::class
        );
    }

    /// Querying

    public function get_last_created_id()
    {
        return $this->last_created_id;
    }

    public function clear_all($collection_name)
    {
        $parameters = [
            'index' => $collection_name,
            'type' => $collection_name,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ],
            'conflicts' => 'proceed',
            'refresh' => true
        ];

        $this->announce_about_to_execute( 'deleteByQuery', $parameters );

        $this->connection_handle->deleteByQuery( $parameters );
    }

    /**
     * Compiles the $query_callable and counts the number of matching records.
     * Returns the number of records.
     */
    public function count($filter_callable = null, $named_parameters = [])
    {
        $query_statement = $this->compile(
                                        function($compiler) use($filter_callable) {

            $compiler->query( function($query) use($filter_callable) {

                $query->eval( $filter_callable );

            });

        });

        $elastic_query = $this->build_elasticsearch_query( $query_statement );

        $collection_name = $elastic_query->get_collection_name();

        $body = $this->replace_named_parameters_in(
            $elastic_query->get_body(),
            $named_parameters
        );

        $count_parameters =[
                'index' => $collection_name,
                'type' => $collection_name,
                'body' => $body
            ];

        if( $elastic_query->get_extra_parameters() !== null ) {
            $count_parameters = array_merge(
                    $count_parameters,
                    $elastic_query->get_extra_parameters()
                );
        }

        $this->announce_about_to_execute( 'count', $count_parameters );

        $result = $this->connection_handle->count( $count_parameters );

        return $result[ 'count' ];
    }

    /// Executing statements

    /**
     * Executes the $query_statement.
     * Returns the result of the execution.
     */
    public function execute_query_statement($query_statement, $named_parameters)
    {
        $elastic_query = $this->build_elasticsearch_query( $query_statement );

        $collection_name = $elastic_query->get_collection_name();

        $body = $this->replace_named_parameters_in(
            $elastic_query->get_body(),
            $named_parameters
        );

        $search_parameters =[
                'index' => $collection_name,
                'type' => $collection_name,
                'body' => $body,
                'sort' => $elastic_query->get_order_by_fields()
            ];

        if( $elastic_query->get_proyected_fields() != null ) {
            $search_parameters[ '_source' ] = $elastic_query->get_proyected_fields();
        }

        if( $elastic_query->get_offset() != null ) {
            $search_parameters[ 'from' ] = $elastic_query->get_offset();
        }

        if( $elastic_query->get_limit() != null ) {
            $search_parameters[ 'size' ] = $elastic_query->get_limit();
        }

        if( $elastic_query->get_extra_parameters() !== null ) {
            $search_parameters = array_merge(
                    $search_parameters,
                    $elastic_query->get_extra_parameters()
                );
        }

        $this->announce_about_to_execute( 'search', $search_parameters );

        $result = $this->connection_handle->search( $search_parameters );

        return $this->process_results_rows( $result );
    }

    public function find_by_id($id, $collection_name, $type = null)
    {
        if( $type === null ) {
            $type = $collection_name;
        }

        $search_parameters = [
                'index' => $collection_name,
                'type' => $type,
                'id' => $id
            ];

        $this->announce_about_to_execute( 'exists', $search_parameters );

        $exists = $this->connection_handle->exists( $search_parameters );

        if( ! $exists ) {
            return null;
        }

        $this->announce_about_to_execute( 'get', $search_parameters );

        $result = $this->connection_handle->get( $search_parameters );

        return $this->process_result_row( $result );
    }

    /**
     * Executes the $create_statement.
     */
    public function execute_create_statement($create_statement, $named_parameters)
    {
        $elastic_query = $this->build_elasticsearch_query( $create_statement );

        $collection_name = $elastic_query->get_collection_name();

        $values = $elastic_query->get_record_values();

        if( ! isset( $values[ "_id" ] ) || $values[ "_id" ] === null ) {
            throw new Haijin_Error( "Must assign an _id." );
        }

        $this->last_created_id = $values[ "_id" ];

        unset( $values[ "_id" ] );

        $create_parameters = [
            'index' => $collection_name,
            'type' => $collection_name,
            'id' => $this->last_created_id,
            'body' => $values,
            'refresh' => true
        ];

        if( $elastic_query->get_extra_parameters() !== null ) {
            $create_parameters = array_merge(
                    $create_parameters,
                    $elastic_query->get_extra_parameters()
                );
        }

        $this->announce_about_to_execute( 'index', $create_parameters );

        $this->connection_handle->index( $create_parameters );
    }

    /**
     * Executes the $update_statement.
     */
    public function execute_update_statement($update_statement, $named_parameters)
    {
        $elastic_query = $this->build_elasticsearch_query( $update_statement );

        $collection_name = $elastic_query->get_collection_name();

        $body_object = $this->replace_named_parameters_in(
            $elastic_query->get_body(),
            $named_parameters
        );

        $body = [];

        if( isset( $body_object->query ) ) {
            $body[ 'query' ] = $body_object->query;
        }
        if( isset( $body_object->script ) ) {
            $body[ 'script' ] = $body_object->script;
        }

        $update_parameters = [
                'index' => $collection_name,
                'type' => $collection_name,
                'body' => $body,
                'refresh' => true
            ];

        if( $elastic_query->get_extra_parameters() !== null ) {
            $update_parameters = array_merge(
                    $update_parameters,
                    $elastic_query->get_extra_parameters()
                );
        }

        $this->announce_about_to_execute( 'updateByQuery', $update_parameters );

        $result = $this->connection_handle->updateByQuery( $update_parameters );
    }

    public function update_by_id($id, $values, $collection_name, $type = null)
    {
        if( $type === null ) {
            $type = $collection_name;
        }

        unset( $values[ "_id" ] );

        $update_parameters = [
            'index' => $collection_name,
            'type' => $type,
            'id' => $id,
            'body' => [
                'doc' => $values
            ],
            'refresh' => true
        ];

        $this->announce_about_to_execute( 'update', $update_parameters );

        $result = $this->connection_handle->update( $update_parameters );
    }

    /**
     * Executes the $delete_statement.
     */
    public function execute_delete_statement($delete_statement, $named_parameters)
    {
        $elastic_query = $this->build_elasticsearch_query( $delete_statement );

        $collection_name = $elastic_query->get_collection_name();

        $body = $this->replace_named_parameters_in(
            $elastic_query->get_body(),
            $named_parameters
        );

        $delete_parameters = [
                'index' => $collection_name,
                'type' => $collection_name,
                'body' => $body,
                'refresh' => true
            ];

        if( $elastic_query->get_extra_parameters() !== null ) {
            $delete_parameters = array_merge(
                    $delete_parameters,
                    $elastic_query->get_extra_parameters()
                );
        }

        $this->announce_about_to_execute( 'deleteByQuery', $delete_parameters );

        $result = $this->connection_handle->deleteByQuery( $delete_parameters );
    }

    public function delete_by_id($id, $collection_name, $type = null)
    {
        if( $type === null ) {
            $type = $collection_name;
        }

        $delete_parameters = [
            'index' => $collection_name,
            'type' => $type,
            'id' => $id,
            'refresh' => true
        ];

        $this->announce_about_to_execute( 'delete', $delete_parameters );

        $this->connection_handle->delete( $delete_parameters );
    }

    protected function process_results_rows($result)
    {
        $rows = [];

        if( isset( $result[ "aggregations" ] ) ) {

            return $result[ "aggregations" ];

        }

        foreach( $result[ "hits" ][ "hits" ]  as $row ) {

            $rows[] = $this->process_result_row( $row );

        }

        return $rows;
    }

    protected function process_result_row($row)
    {
        $row[ "_source" ][ "_id" ] = $row[ "_id" ];

        return $row[ "_source" ];
    }

    protected function replace_named_parameters_in($query, $named_parameters)
    {
        if( empty( $named_parameters ) ) {
            return $query;
        }

        if( is_array( $query ) ) {
            $iterable = $query;
        } elseif( is_object( $query ) ) {
            $iterable = get_object_vars( $query );
        } else {
            $iterable = [];
        }

        foreach( $iterable as $key => $value ) {

            if( is_a( $value, Named_Parameter_Placerholder::class ) ) {

                if( ! isset( $named_parameters[ $value->get_parameter_name() ] ) ) {
                    $this->raise_named_parameter_not_found_error(
                            $value->get_parameter_name()
                        );
                }

                $named_value = $named_parameters[ $value->get_parameter_name() ];

                if( is_array( $query ) ) {

                    $query[ $key ] = $named_value;

                } elseif( is_a( $query, \stdclass::class ) ) {

                    $query->$key = $named_value;

                }

            } elseif( is_array( $query ) ) {

                $query[ $key ] = $this->replace_named_parameters_in( $value, $named_parameters );

            } elseif( is_a( $query, \stdclass::class ) ) {

                $query->$key = $this->replace_named_parameters_in( $value, $named_parameters );

            }

        }

        return $query;
    }

    /// Query building

    protected function build_elasticsearch_query($statement)
    {
        $elastic_query_builder = $this->new_elasticsearch_query_builder();

        $elastic_query_builder->visit( $statement );

        return $elastic_query_builder;
    }

    protected function new_elasticsearch_query_builder()
    {
        return Create::an( Elasticsearch_Query_Builder::class )->with();
    }

    /// Double disptach

    public function visit($visitor)
    {
        return $visitor->accept_elasticsearch_database( $this );
    }

    /// Annnouncing

    protected function announce_about_to_execute($endpoint, $parameters)
    {
        $this->announce(
            new About_To_Execute_Elasticsearch_Statement( $endpoint, $parameters )
        );
    }
}