<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Errors\Persistency_Error;
use Haijin\Persistency\Errors\Query_Expressions\Invalid_Expression_Error;
use Haijin\Announcements\Announcer_Trait;
use Haijin\Persistency\Announcements\About_To_Create_Object;
use Haijin\Persistency\Announcements\About_To_Update_Object;
use Haijin\Persistency\Announcements\About_To_Delete_Object;
use Haijin\Persistency\Announcements\Object_Created;
use Haijin\Persistency\Announcements\Object_Updated;
use Haijin\Persistency\Announcements\Object_Deleted;
use Haijin\Persistency\Announcements\Object_Creation_Canceled;
use Haijin\Persistency\Announcements\Object_Update_Canceled;
use Haijin\Persistency\Announcements\Object_Deletion_Canceled;

abstract class Persistent_Collection
{
    use Announcer_Trait;

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

        });
    }

    /// Definition

    public function define($callable)
    {
        $definition_dsl = Create::object( Persistent_Collection_DSL::class, $this );

        $callable( $definition_dsl );
    }

    public function definition($definition_dsl)
    {
    }

    /// Accessing

    public function get_database()
    {
        $this->validate_database();

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

    public function exists_with_id($id) {
        return $this->exists_with(
            [ $this->get_id_field() => $id ]
        );
    }

    abstract public function exists_with($field_values);

    abstract public function exists($filter_callable);

    public function find_by_id($id, $named_parameters = [], $eager_fetch = [])
    {
        $this->validate_named_parameters( $named_parameters );

        return $this->find_by(
            [
                $this->get_id_field() => $id
            ],
            $named_parameters,
            $eager_fetch
        );
    }

    abstract public function find_all_by_ids(
        $ids_collection, $named_parameters = [], $eager_fetch = []
    );

    public function find_by_id_if_absent(
            $id, $absent_callable, $named_parameters = [], $eager_fetch = []
        )
    {
        $this->validate_named_parameters( $named_parameters );

        $object = $this->find_by_id( $id, $named_parameters, $eager_fetch );

        if( $object !== null ) {
            return $object;
        }

        return $absent_callable( $id );
    }

    abstract public function find_by(
        $field_values, $named_parameters = [], $eager_fetch = []
    );

    public function find_by_if_absent(
            $field_values, $absent_callable, $named_parameters = [], $eager_fetch = []
        )
    {
        $this->validate_named_parameters( $named_parameters );

        $object = $this->find_by( $field_values, $named_parameters, $eager_fetch );

        if( $object !== null ) {
            return $object;
        }

        return $absent_callable( $field_values );
    }

    /// Querying

    /**
     * Returns all the objects matching the optional $filter_callable.
     * If not $filter_callable is given returns all the object in the collection, impractical
     * in a real application but valuable for testing and debugging applications.
     *
     * The $filter_query is a Query_Statement callable with no collection expression, which
     * is suppllied by $this Persistent_Collection.
     */
    public function all($filter_callable = null, $named_parameters = [], $eager_fetch = [])
    {
        $this->validate_named_parameters( $named_parameters );

        if( $filter_callable === null ) {

            $filter_callable = function($query) {

                $id_field = $this->get_id_field();

                $query->order_by(
                    $query ->field( $id_field )
                );

            };

        }

        $records = $this->get_database()->query( function($query) use($filter_callable) {

            $query->set_meta_model( $this );

            $query->collection( $this->collection_name );

            $filter_callable( $query );

        }, $named_parameters);

        $objects = $this->records_to_objects( $records );

        return $this->process_returned_objects(
            $objects,
            $named_parameters,
            $eager_fetch
        );
    }

    /**
     * Returns the first object in the collection or null if there is none.
     */
    public function first(
            $filter_callable = null, $named_parameters = [], $eager_fetch = []
        )
    {
        $this->validate_named_parameters( $named_parameters );

        if( $filter_callable === null ) {

            $filter_callable = function($query) {

                $query->order_by(
                    $query ->field( $this->get_id_field() )
                );

            };

        }

        $objects = $this->all( function($query) use($filter_callable) {

            if( $filter_callable !== null ) {

                $filter_callable( $query );

            }

            $query->pagination(
                $query->limit( 1 )
            );

        }, $named_parameters, $eager_fetch );

        if( empty( $objects ) ) {
            return null;
        }

        return $objects[ 0 ];
    }

    /**
     * Returns the last object in the collection or null if there is none.
     */
    public function last($eager_fetch = [])
    {
        return $this->first( function($query) {

            $query->set_meta_model( $this );

            $query->collection( $this->collection_name );

            $query->order_by(
                $query ->field($this->get_id_field() ) ->desc()
            );

            $query->pagination(
                $query->limit( 1 )
            );

        }, [], $eager_fetch );
    }

    /// Counting

    /**
     * Compiles the $query_callable and counts the number of matching records.
     * Returns the number of records.
     */
    public function count($filter_callable = null, $named_parameters = [])
    {
        $this->validate_named_parameters( $named_parameters );

        return $this->get_database()->count( function($query) use($filter_callable) {

            $query->set_meta_model( $this );

            $query->collection( $this->collection_name );

            if( $filter_callable !== null ) {
                $filter_callable( $query );
            }

        }, $named_parameters );
    }

    /// Creating

    public function create($object)
    {
        $creation_announcement = $this->announce_about_to_create_object( $object );

        if( $creation_announcement->was_canceled() ) {

            $this->announce_object_creation_canceled(
                $object, $creation_announcement->get_cancelation_reasons()
            );

            return;
        }

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

        $this->announce_object_created( $object );

        return $object;
    }

    public function create_from_attributes($attributes)
    {
        if( ! is_array( $attributes ) ) {
            throw new Invalid_Expression_Error(
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
            throw new Invalid_Expression_Error(
                "insert_record() expects an associative array."
            );
        }

        $this->get_database()->create( function($query) use($record_values) {

            $query->set_meta_model( $this );

            $query->collection( $this->collection_name );

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
            throw new Invalid_Expression_Error(
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

    abstract public function update($object);

    public function update_all($filter_callable, $named_parameters = [])
    {
        $this->validate_named_parameters( $named_parameters );

        $records = $this->get_database()->update( function($query) use($filter_callable) {

            $query->set_meta_model( $this );

            $query->collection( $this->collection_name );

            $filter_callable( $query );

        }, $named_parameters);

        return $this;
    }

    /// Deleting

    public function clear_all()
    {
        $this->get_database()->clear_all( $this->collection_name );
    }

    abstract public function delete($object);

    public function delete_all($filter_callable, $named_parameters = [])
    {
        $this->validate_named_parameters( $named_parameters );

        $records = $this->get_database()->delete( function($query) use($filter_callable) {

            $query->set_meta_model( $this );

            $query->collection( $this->collection_name );

            $filter_callable( $query );

        }, $named_parameters);

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

        if( is_callable( $this->objects_instantiator ) ) {
            return ( $this->objects_instantiator )( $raw_record );
        }

        if( $this->objects_instantiator === null ) {
            return $raw_record;
        }

        $this->raise_unkown_instantiator_error();
    }

    protected function process_returned_objects($objects, $named_parameters, $eager_fetch)
    {
        if( empty( $eager_fetch ) ) {
            return $objects;
        }

        return $this->eager_fetch( $objects, $eager_fetch );
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

        throw new Invalid_Expression_Error(
            "Expected '\$named_parameters' to be an associative array."
        );
    }

    /// Announcing

    protected function announce_about_to_create_object($object)
    {
        $announcement = new About_To_Create_Object( $object );

        $this->announce( $announcement );

        return $announcement;
    }

    protected function announce_object_created($object)
    {
        $announcement = new Object_Created( $object );

        $this->announce( $announcement );

        return $announcement;
    }

    protected function announce_object_creation_canceled($object, $cancelation_reasons)
    {
        $this->announce(
            new Object_Creation_Canceled( $object, $cancelation_reasons )
        );
    }

    protected function announce_about_to_update_object($object)
    {
        $announcement = new About_To_Update_Object( $object );

        $this->announce( $announcement );

        return $announcement;
    }

    protected function announce_object_updated($object)
    {
        $announcement = new Object_Updated( $object );

        $this->announce( $announcement );

        return $announcement;
    }

    protected function announce_object_update_canceled($object, $cancelation_reasons)
    {
        $this->announce(
            new Object_Update_Canceled( $object, $cancelation_reasons )
        );
    }

    protected function announce_about_to_delete_object($object)
    {
        $announcement = new About_To_Delete_Object( $object );

        $this->announce( $announcement );

        return $announcement;
    }

    protected function announce_object_deleted($object)
    {
        $announcement = new Object_Deleted( $object );

        $this->announce( $announcement );

        return $announcement;
    }

    protected function announce_object_deletion_canceled($object, $cancelation_reasons)
    {
        $this->announce(
            new Object_Deletion_Canceled( $object, $cancelation_reasons )
        );
    }

    /// Validating

    protected function validate_database()
    {
        if( $this->database !== null ) {
            return;
        }

        $class_name = get_class( $this );

        throw new Persistency_Error(
            "{$class_name} must '->set_database(\$database)' first."
        );
    }

    /// Raising errors

    protected function raise_missing_primary_key_error()
    {
        throw new Invalid_Expression_Error(
            "Missing a primary key field in the definition."
        );
    }

    protected function raise_unkown_instantiator_error()
    {
        throw new Invalid_Expression_Error( "Unkown instantiator." );
    }

    protected function raise_more_than_one_record_found_error($found_count)
    {
        throw new Persistency_Error( "Expected one record, found {$found_count}." );
    }

    protected function raise_field_mapping_not_found_error($field_name)
    {
        $class_name = get_class( $this );

        throw new Invalid_Expression_Error(
            "Field mapping at field '{$field_name}' in class {$class_name} not found."
        );        
    }
}
