<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Instantiator\Global_Factory;
use Haijin\Instantiator\Create;
use Haijin\Persistency\Database\Database;
use Elasticsearch\ClientBuilder;
use Haijin\Persistency\Types_Converters\Null_Converter;
use Haijin\Persistency\Engines\Named_Parameter_Placerholder;

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
        $closure = $params[ 0 ];

        if( isset( $params[ 1 ] ) ) {
            $binding = $params[ 1 ];
        } else {
            $binding = null;
        }

        $this->connection_handle = ClientBuilder::create();

        $this->with_handle_do( $closure, $binding );

        $this->connection_handle = $this->connection_handle->build();

        return $this;
    }

    public function with_handle_do($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        return $closure->call( $binding, $this->connection_handle );
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

    /// Querying

    public function get_last_created_id()
    {
        return $this->last_created_id;
    }

    public function clear_all($collection_name)
    {
        $this->connection_handle->deleteByQuery([
            'index' => $collection_name,
            'type' => $collection_name,
            'body' => [ "query" => [
                    "match_all" => new \stdClass()
                ]
            ],
            'conflicts' => 'proceed',
            'refresh' => true
        ]);
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

        $additional_query_params = [];

        if( isset( $named_parameters[ "_elastic" ] ) ) {
            $additional_query_params = $named_parameters[ "_elastic" ];
            unset( $named_parameters[ "_elastic" ] );
        }

        $body = $this->replace_named_parameters_in(
            $elastic_query->get_body(),
            $named_parameters
        );

        $search_parameters = array_merge(
            [
                'index' => $collection_name,
                'type' => $collection_name,
                'body' => $body,
                'sort' => $elastic_query->get_order_by_fields()
            ],
            $additional_query_params
        );

        if( $elastic_query->get_proyected_fields() != null ) {
            $search_parameters[ '_source' ] = $elastic_query->get_proyected_fields();
        }

        if( $elastic_query->get_offset() != null ) {
            $search_parameters[ 'from' ] = $elastic_query->get_offset();
        }

        if( $elastic_query->get_limit() != null ) {
            $search_parameters[ 'size' ] = $elastic_query->get_limit();
        }

        $result = $this->connection_handle->search( $search_parameters );

        return $this->process_results_rows( $result );
    }

    public function find_by_id($id, $collection_name, $type = null)
    {
        if( $type == null ) {
            $type = $collection_name;
        }

        $exists = $this->connection_handle->exists([
                'index' => $collection_name,
                'type' => $type,
                'id' => $id
            ]);

        if( ! $exists ) {
            return null;
        }

        $result = $this->connection_handle->get([
                'index' => $collection_name,
                'type' => $type,
                'id' => $id
            ]);

        return $this->process_result_row( $result );
    }

    /**
     * Executes the $create_statement.
     */
    public function execute_create_statement($create_statement, $named_parameters)
    {
        $elastic_query = $this->build_elasticsearch_query( $create_statement );

        $collection_name = $elastic_query->get_collection_name();

        $additional_query_params = [];

        if( isset( $named_parameters[ "_elastic" ] ) ) {
            $additional_query_params = $named_parameters[ "_elastic" ];
            unset( $named_parameters[ "_elastic" ] );
        }

        $values = $elastic_query->get_record_values();

        if( ! isset( $values[ "_id" ] ) || $values[ "_id" ] === null ) {
            throw new \RuntimeException( "Must assign an _id." );
        }

        $this->last_created_id = $values[ "_id" ];

        unset( $values[ "_id" ] );

        $create_parameters = array_merge(
            [
                'index' => $collection_name,
                'type' => $collection_name,
                'id' => $this->last_created_id,
                'body' => $values,
                'refresh' => true
            ],
            $additional_query_params
        );

        $this->connection_handle->index( $create_parameters );
    }

    /**
     * Executes the $update_statement.
     */
    public function execute_update_statement($update_statement, $named_parameters)
    {

        throw new \RuntimeException( "Currently not supported." );

    }

    public function update_by_id($id, $values, $collection_name, $type = null)
    {
        if( $type == null ) {
            $type = $collection_name;
        }

        unset( $values[ "_id" ] );

        $result = $this->connection_handle->update([
                'index' => $collection_name,
                'type' => $type,
                'id' => $id,
                'body' => [
                    'doc' => $values
                ],
                'refresh' => true
            ]);
    }

    /**
     * Executes the $delete_statement.
     */
    public function execute_delete_statement($delete_statement, $named_parameters)
    {
        $elastic_query = $this->build_elasticsearch_query( $delete_statement );

        $collection_name = $elastic_query->get_collection_name();

        $additional_query_params = [];

        if( isset( $named_parameters[ "_elastic" ] ) ) {
            $additional_query_params = $named_parameters[ "_elastic" ];
            unset( $named_parameters[ "_elastic" ] );
        }

        $body = $this->replace_named_parameters_in(
            $elastic_query->get_body(),
            $named_parameters
        );

        $delete_parameters = array_merge(
            [
                'index' => $collection_name,
                'type' => $collection_name,
                'body' => $body,
                'refresh' => true
            ],
            $additional_query_params
        );

        $result = $this->connection_handle->deleteByQuery( $delete_parameters );
    }

    public function delete_by_id($id, $collection_name, $type = null)
    {
        if( $type == null ) {
            $type = $collection_name;
        }

        $result = $this->connection_handle->delete([
                'index' => $collection_name,
                'type' => $type,
                'id' => $id,
                'refresh' => true
            ]);
    }

    protected function process_results_rows($result)
    {
        $rows = [];

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

        foreach( $query as $key => $value) {

            if( is_a( $value, Named_Parameter_Placerholder::class ) ) {

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

    protected function new_update_expression_compiler()
    {
        return Create::a( Elasticsearch_Update_Statement_Compiler::class )->with();
    }

    protected function new_elasticsearch_query_builder()
    {
        return Create::an( Elasticsearch_Query_Builder::class )->with();
    }

    /// Debugging

    /**
     * Evaluates the $closure with the debugging information about the built query.
     * Each Database subclass defines the $closure parameters. For instance, for
     * sql database one parameter can be the built sql string.
     * This method is intenteded for debugging purposes, not to use in production.
     *
     * @param $query Query_Statement_Compiler The parameter of the query_closure.
     * @param closure $closure A closure with the debugging information as its parametets.
     * @param object $binding Optional - An optional object to bind to the evaluation of
     *      the $closure. If none is given the $closure is bound to $this object.
     *
     * Example of use:
     *
     *      $database->query( function($query) use($database) {
     *
     *      $query->collection( "users" );
     *
     *      $database->inspect_query( $query, function($sql, $query_parameters) {
     *          var_dump( $sql );
     *          var_dump( $query_parameters );
     *      });
     *  });
     *
     */
    public function inspect_query($query, $closure, $binding = null)
    {
        throw new \Exception( "inspect_query" );
    }

}