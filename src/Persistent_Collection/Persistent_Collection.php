<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Errors\Persistency_Error;

class Persistent_Collection
{
    protected $database;
    protected $collection_name;
    protected $objects_instantiator;
    protected $field_mappings;

    /// Initializing

    public function __construct()
    {
        $this->database = null;
        $this->collection_name = null;
        $this->objects_instantiator = null;
        $this->field_mappings = [];

        $this->initialize_definition();
    }

    protected function initialize_definition()
    {
        $this->define( function($config) {

            $this->definition( $config );

        }, $this );
    }

    /// Definition

    public function define($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        $definition_dsl = Create::object( Persistent_Collection_DSL::class,  $this );

        $closure->call($binding, $definition_dsl);
    }

    public function definition($definition_dsl)
    {
    }

    /// Accessing

    public function get_database()
    {
        return $this->database;
    }

    public function set_database($database)
    {
        $this->database = $database;
    }

    public function get_collection_name()
    {
        return $this->collection_name;
    }

    public function set_collection_name($collection_name)
    {
        $this->collection_name = $collection_name;
    }

    public function set_objects_instantiator($class_name_or_block)
    {
        $this->objects_instantiator = $class_name_or_block;
    }

    public function add_field_mapping($field_mapping)
    {
        $this->field_mappings[ $field_mapping->get_field_name() ] = $field_mapping;
    }

    public function get_field_mapping_at($field_name)
    {
        if( ! isset( $this->field_mappings[ $field_name ] ) ) {
            $this->raise_field_mapping_not_found_error( $field_name );
        }

        return $this->field_mappings[ $field_name ];
    }

    public function get_id_field()
    {
        return $this->get_primary_key_field_mapping()->get_field_name();
    }

    public function get_id_of($object)
    {
        return $this->get_primary_key_field_mapping()->read_value_from( $object );
    }

    public function get_primary_key_field_mapping()
    {
        foreach( $this->field_mappings as $field_mapping ) {
            if( $field_mapping->is_primary_key() ) {
                return $field_mapping;
            }
        }

        $this->raise_missing_primary_key_error();
    }

    public function get_record_values_from($object)
    {
        $values = [];

        foreach( $this->field_mappings as $field_mapping ) {

            if( $field_mapping->writes_to_database() === false ) {
                continue;
            }

            $value = $field_mapping->read_value_from( $object );

            $value = $field_mapping->convert_value_to_db( $value, $this->database );

            if( $field_mapping->is_primary_key() && $value === null ) {
                continue;
            }

            $values[ $field_mapping->get_field_name() ] = $value;

        }

        return $values;
    }

    /// Searching

    public function find_by_id($id, $named_parameters = [])
    {
        $this->validate_named_parameters( $named_parameters );

        return $this->find_by([
            $this->get_id_field() => $id
        ]);
    }

    public function find_all_by_ids($ids_collection, $named_parameters = [])
    {
        $this->validate_named_parameters( $named_parameters );

        $id_field = $this->get_id_field();

        return $this->all( function($query) use($id_field, $ids_collection) {

            $query->filter(
                $query->field( $id_field ) ->in( $ids_collection )
            );

        }, $named_parameters );
    }

    public function find_by_id_if_absent(
            $id, $absent_closure, $named_parameters = [], $binding = null
        )
    {
        $this->validate_named_parameters( $named_parameters );

        $object = $this->find_by_id( $id, $named_parameters );

        if( $object !== null ) {
            return $object;
        }

        if( $binding === null ) {
            $binding = $this;
        }

        return $absent_closure->call( $binding, $id );
    }

    public function find_by($field_values, $named_parameters = [])
    {
        $this->validate_named_parameters( $named_parameters );

        $found_objects = $this->all( function($query) use($field_values) {

            $first_expression = true;
            $expression = null;

            foreach( $field_values as $field_name => $value ) {

                if( $first_expression === true ) {

                    $expression = $query ->brackets(
                        $query ->field( $field_name ) ->op( "=" ) ->value( $value )
                    );

                    $first_expression = false;

                } else {

                    $expression = $expression ->and() ->brackets(
                        $query ->field( $field_name ) ->op( "=" ) ->value( $value )
                    );

                }
            }

            $query->filter( $expression );

        }, $named_parameters );

        $found_count = count( $found_objects );

        if( $found_count == 0 ) {
            return null;
        }

        if( $found_count == 1 ) {
            return $found_objects[ 0 ];
        }

        $this->raise_more_than_one_record_found_error($found_count);
    }

    public function find_by_if_absent(
            $field_values, $absent_closure, $named_parameters = [], $binding = null
        )
    {
        $this->validate_named_parameters( $named_parameters );

        $object = $this->find_by( $field_values, $named_parameters );

        if( $object !== null ) {
            return $object;
        }

        if( $binding === null ) {
            $binding = $this;
        }

        return $absent_closure->call( $binding, $field_values );
    }

    /// Querying

    /**
     * Returns all the objects matching the optional $filter_closure.
     * If not $filter_closure is given returns all the object in the collection, impractical
     * in a real application but valuable for testing and debugging applications.
     *
     * The $filter_query is a Query_Statement closure with no collection expression, which
     * is suppllied by $this Persistent_Collection.
     */
    public function all($filter_closure = null, $named_parameters = [], $binding = null)
    {
        $this->validate_named_parameters( $named_parameters );

        if( $binding === null ) {
            $binding = $this;
        }

        if( $filter_closure === null ) {

            $id_field = $this->get_id_field();

            $filter_closure = function($query) use($id_field) {

                $query->order_by(
                    $query ->field( "id" )
                );

            };

        }

        $collection_name = $this->collection_name;

        $records = $this->get_database()->query( function($query)
                                            use($collection_name, $filter_closure) {
            $query->collection( $collection_name );

            $filter_closure->call( $this, $query );

        }, $named_parameters, $binding );

        $objects = $this->records_to_objects( $records );

        return $this->process_returned_objects( $objects, $named_parameters );
    }

    /**
     * Returns the first object in the collection or null if there is none.
     */
    public function first($filter_closure = null, $named_parameters = [], $binding = null)
    {
        $this->validate_named_parameters( $named_parameters );

        if( $binding === null ) {
            $binding = $this;
        }

        if( $filter_closure === null ) {

            $id_field = $this->get_id_field();

            $filter_closure = function($query) use($id_field) {

                $query->order_by(
                    $query ->field( $id_field )
                );

            };

        }

        $objects = $this->all( function($query) use($filter_closure) {

            if( $filter_closure !== null ) {

                $filter_closure->call( $this, $query );

            }

            $query->pagination(
                $query->limit( 1 )
            );

        }, $named_parameters, $binding );

        if( empty( $objects ) ) {
            return null;
        }

        return $objects[ 0 ];
    }

    /**
     * Returns the last object in the collection or null if there is none.
     */
    public function last()
    {
        $collection_name = $this->collection_name;

        $id_field = $this->get_id_field();

        return $this->first( function($query) use($collection_name, $id_field) {

            $query->collection( $collection_name );

            $query->order_by(
                $query ->field( $id_field ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        });
    }

    /// Counting

    /**
     * Compiles the $query_closure and counts the number of matching records.
     * Returns the number of records.
     */
    public function count($filter_closure = null, $named_parameters = [], $binding = null)
    {
        $this->validate_named_parameters( $named_parameters );

        $collection_name = $this->collection_name;

        return $this->get_database()->count( function($query)
                                            use($collection_name, $filter_closure) {

            $query->collection( $collection_name );

            if( $filter_closure !== null ) {
                $filter_closure->call( $this, $query );
            }

        }, $named_parameters, $binding );
    }

    /// Creating

    public function create($object)
    {
        $record_values = $this->insert_record(

            $this->get_record_values_from( $object )

        );

        if( $this->get_id_of( $object ) === null ) {

            $primary_key_mapping = $this->get_primary_key_field_mapping();

            $primary_key_mapping->write_value_to(
                $object,
                $record_values[ $primary_key_mapping->get_field_name() ],
                $record_values,
                $record_values
            );

        }

        return $object;
    }

    public function create_from_attributes($attributes)
    {
        if( ! is_array( $attributes ) ) {
            throw new \RuntimeException(
                "create_from_attributes() expects an associative array."
            );
        }

        $object = $this->instantiate_object( $attributes );

        foreach( $this->field_mappings as $mapping ) {

            $field_name = $mapping->get_field_name();

            if( ! array_key_exists( $field_name, $attributes ) ) {
                continue;
            }

            $mapping->write_value_to(
                $object,
                $attributes[ $field_name ],
                $attributes,
                $attributes
            );
        }

        return $this->create( $object );
    }

    public function insert_record($record_values)
    {
        if( ! is_array( $record_values ) ) {
            throw new \RuntimeException(
                "insert_record() expects an associative array."
            );
        }

        $collection_name = $this->collection_name;

        $this->get_database()->create( function($query)
                                    use($collection_name, $record_values) {

            $query->collection( $collection_name );

            $expressions = [];
            foreach( $record_values as $field => $value ) {
                $expressions[] = $query->set( $field, $query->value( $value ) );
            }

            $query->record( ...$expressions );

        });

        $id_field_name = $this->get_id_field();

        if( ! isset( $record_values[ $id_field_name ] )
            ||
            $record_values[ $id_field_name ] === null
          ) {

            $record_values[ $id_field_name ] =
                $this->get_database()->get_last_created_id();
        }

        return $record_values;
    }

    /// Updating

    public function update_from_attributes($object, $attributes)
    {
        if( ! is_array( $attributes ) ) {
            throw new \RuntimeException(
                "create_from_attributes() expects an associative array."
            );
        }

        foreach( $this->field_mappings as $mapping ) {

            $field_name = $mapping->get_field_name();

            if( ! array_key_exists( $field_name, $attributes ) ) {
                continue;
            }

            $mapping->write_value_to(
                $object,
                $attributes[ $field_name ],
                $attributes,
                $attributes
            );
        }

        return $this->update( $object );
    }

    public function update($object)
    {
        $id_field = $this->get_id_field();
        $id = $this->get_id_of( $object );
        $record_values = $this->get_record_values_from( $object );

        $collection_name = $this->collection_name;

        $this->get_database()->update( function($query)
                                    use($collection_name, $id_field, $id, $record_values) {

            $query->collection( $collection_name );

            $expressions = [];
            foreach( $record_values as $field => $value ) {
                $expressions[] = $query->set( $field, $query->value( $value ) );
            }

            $query->record( ...$expressions );

            $query->filter(
                $query ->field( $id_field ) ->op( "=" ) ->value( $id )
            );

        });

        return $object;
    }

    public function update_all($filter_closure, $named_parameters = [], $binding = null)
    {
        $this->validate_named_parameters( $named_parameters );

        $collection_name = $this->collection_name;

        $records = $this->get_database()->update( function($query)
                                            use($collection_name, $filter_closure) {

            $query->collection( $collection_name );

            $filter_closure->call( $this, $query );

        }, $named_parameters, $binding );

        return $this;
    }

    /// Deleting

    public function clear_all()
    {
        $this->get_database()->clear_all( $this->collection_name );
    }

    public function delete($object)
    {
        $id_field = $this->get_id_field();

        $id = $this->get_id_of( $object );

        $collection_name = $this->collection_name;

        $this->get_database()->delete( function($query)
                                    use($collection_name, $id_field, $id) {

            $query->collection( $collection_name );

            $query->filter(
                $query ->field( $id_field ) ->op( "=" ) ->value( $id )
            );

        }, [] );

        return $object;
    }

    public function delete_all($filter_closure, $named_parameters = [], $binding = null)
    {
        $this->validate_named_parameters( $named_parameters );

        $collection_name = $this->collection_name;

        $records = $this->get_database()->delete( function($query)
                                            use($collection_name, $filter_closure) {

            $query->collection( $collection_name );

            $filter_closure->call( $this, $query );

        }, $named_parameters, $binding );

        return $this;
    }

    /// Converting

    /**
     * Maps an array of records to an array of objects.
     */
    protected function records_to_objects($records)
    {
        $objects = [];

        foreach( $records as $record ) {
            $objects[] = $this->record_to_object( $record );
        }

        return $objects;
    }

    /**
     * Maps a single record to a object.
     */
    protected function record_to_object($raw_record)
    {
        if( $raw_record === null ) {
            return null;
        }

        $mapped_record = [];

        $object = $this->instantiate_object( $raw_record );

        $object_id = $raw_record[ $this->get_id_field() ];

        foreach( $this->field_mappings as $field_mapping ) {

            $field_name = $field_mapping->get_field_name();

            $mapped_value = $field_mapping->convert_value_from_db(
                $raw_record,
                $object,
                $field_name,
                $this,
                $this->database
            );

            $mapped_record[ $field_name ] = $mapped_value;

        }

        foreach( $this->field_mappings as $field_mapping ) {

            $field_mapping->write_value_to(
                $object,
                $mapped_record[ $field_mapping->get_field_name() ],
                $mapped_record,
                $raw_record
            );

        }

        return $object;
    }

    /**
     * Maps a single record to a object.
     */
    protected function instantiate_object($raw_record)
    {
        if( is_string( $this->objects_instantiator ) ) {
            return Create::object( $this->objects_instantiator);
        }

        if( is_a( $this->objects_instantiator, \Closure::class ) ) {
            return $this->objects_instantiator->call( $this, $raw_record );
        }

        if( $this->objects_instantiator === null ) {
            return $raw_record;
        }

        $this->raise_unkown_instantiator_error();
    }

    protected function process_returned_objects($objects, $named_parameters)
    {
        if( ! isset( $named_parameters[ 'eager_fetch'] ) ) {
            return $objects;
        }

        return $this->eager_fetch( $objects, $named_parameters[ 'eager_fetch' ] );
    }

    protected function eager_fetch($objects, $fetch_spec)
    {
        $eager_fetcher = new Eager_Fetcher();

        $eager_fetcher->resolve_references_in_collection( $this, $objects, $fetch_spec );

        return $objects;
    }

    /// Validating

    protected function validate_named_parameters($named_parameters) {
        if( is_array( $named_parameters ) ) {
            return;
        }

        throw new \RuntimeException( "Expected '\$named_parameters' to be an associative array." );
    }


    /// Raising errors

    protected function raise_missing_primary_key_error()
    {
        throw new \RuntimeException( "Missing a primary key field in the definition." );
    }

    protected function raise_unkown_instantiator_error()
    {
        throw new \RuntimeException( "Unkown instantiator." );
    }

    protected function raise_more_than_one_record_found_error($found_count)
    {
        throw new Persistency_Error( "Expected one record, found {$found_count}." );
    }

    protected function raise_field_mapping_not_found_error($field_name)
    {
        $class_name = get_class( $this );

        throw new \RuntimeException(
            "Field mapping at field '{$field_name}' in class {$class_name} not found."
        );        
    }
}
