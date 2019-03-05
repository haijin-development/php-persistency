<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;
use \Haijin\Persistency\Persistent_Collection\Object_Accessors\Method_Accessor;
use \Haijin\Persistency\Persistent_Collection\Object_Accessors\Property_Accessor;
use \Haijin\Persistency\Persistent_Collection\Object_Accessors\Array_Accessor;
use \Haijin\Persistency\Persistent_Collection\Object_Accessors\Closure_Accessor;

use \Haijin\Persistency\Persistent_Collection\Field_Types\Value_Type;
use \Haijin\Persistency\Persistent_Collection\Field_Types\Reference_To_Object_In_Collection_Type;
use \Haijin\Persistency\Persistent_Collection\Field_Types\Reference_From_Object_In_Collection_Type;
use \Haijin\Persistency\Persistent_Collection\Field_Types\Reference_Collection_From_Collection_Type;
use \Haijin\Persistency\Persistent_Collection\Field_Types\Reference_Collection_Through_Collection_Type;

class Persistent_Collection_DSL
{
    protected $persistent_collection;
    protected $current_field_mapping;

    /// Initializing

    public function __construct($persistent_collection)
    {
        $this->persistent_collection = $persistent_collection;
        $this->current_field_mapping = null;
    }

    /// DSL

    public function database($database)
    {
        $this->persistent_collection->set_database( $database );
    }

    public function collection_name($collection_name)
    {
        $this->persistent_collection->set_collection_name( $collection_name );
    }

    public function instantiate_objects_with($class_name_or_closure)
    {
        if( $class_name_or_closure !== null
            &&
            ! is_string( $class_name_or_closure )
            &&
            ! is_a( $class_name_or_closure, \Closure::class )
          ) {
            $this->raise_unexpected_instantiator_error();
        }

        $this->persistent_collection->set_objects_instantiator( $class_name_or_closure );
    }

    public function field_mappings($closure)
    {
        $closure->call( $this, $this );
    }

    public function __set($attribute_name, $value)
    {
        if( $attribute_name == 'database' ) {
            $this->database( $value );
            return;
        }

       if( $attribute_name == 'collection_name' ) {
            $this->collection_name( $value );
            return;
        }

       if( $attribute_name == 'instantiate_objects_with' ) {
            $this->instantiate_objects_with( $value );
            return;
        }

       if( $attribute_name == 'field_mappings' ) {
            $this->field_mappings( $value );
            return;
        }

        $this->raise_unexpected_definition_error( $attribute_name );        
    }

    /// Fields DSL

    public function field($field_name)
    {
        $this->current_field_mapping = new Field_Mapping( $field_name );

        $this->persistent_collection->add_field_mapping( $this->current_field_mapping );

        return $this;
    }

    public function is_primary_key()
    {
        $this->current_field_mapping->set_is_primary_key( true );

        return $this;
    }

    public function type($type)
    {
        $this->current_field_mapping->set_type( new Value_Type( $type ) );

        return $this;
    }

    public function reference_to(
            $persistent_collection, $config = []
        )
    {
        $this->current_field_mapping->set_type(
            new Reference_To_Object_In_Collection_Type( $persistent_collection, $config )
        );

        return $this;
    }

    public function reference_from(
            $other_persistent_collection, $other_id_field, $config = []
        )
    {
        $this->current_field_mapping->set_type(
            new Reference_From_Object_In_Collection_Type(
                $other_persistent_collection,
                $other_id_field,
                $config
            )
        );

        return $this;
    }

    public function reference_collection_from(
            $other_persistent_collection, $other_id_field, $config = []
        )
    {
        $this->current_field_mapping->set_type(
            new Reference_Collection_From_Collection_Type(
                $other_persistent_collection,
                $other_id_field,
                $config
            )
        );

        return $this;
    }

    public function reference_collection_through(
            $middle_table, $left_id_field, $right_id_field, $other_collection, $config = []
        )
    {
        $this->current_field_mapping->set_type(
            new Reference_Collection_Through_Collection_Type(
                $middle_table,
                $left_id_field,
                $right_id_field,
                $other_collection,
                $config
            )
        );

        return $this;
    }

    public function write_to_database($boolean)
    {
        $this->current_field_mapping->set_writes_to_database( $boolean );

        return $this;
    }

    public function read_with($value_reader)
    {
        $value_accessor = null;

        if( is_string( $value_reader) ) {

            if( $method_accessor = $this->is_method_accessor( $value_reader ) ) {
                $value_accessor = Create::a( Method_Accessor::class )->with( $method_accessor );
            }

            if( $property_accessor = $this->is_property_accessor( $value_reader ) ) {
                $value_accessor = Create::a( Property_Accessor::class )->with( $property_accessor );
            }

            if( $array_accessor = $this->is_array_accessor( $value_reader ) ) {
                $value_accessor = Create::a( Array_Accessor::class )->with( $array_accessor );
            }
        }

        if( is_a( $value_reader, \Closure::class ) ) {
            $value_accessor = Create::a( Closure_Accessor::class )->with( $value_reader );
        }

        if( $value_accessor === null ) {
            $this->raise_unexpected_read_with_value_error( $value_reader );
        }

        $this->current_field_mapping->set_value_reader( $value_accessor );

        return $this;
    }

    public function write_with($value_writter)
    {
        $value_accessor = null;

        if( is_string( $value_writter) ) {

            if( $method_accessor = $this->is_method_accessor( $value_writter ) ) {
                $value_accessor = Create::a( Method_Accessor::class )->with( $method_accessor );
            }

            if( $property_accessor = $this->is_property_accessor( $value_writter ) ) {
                $value_accessor = Create::a( Property_Accessor::class )->with( $property_accessor );
            }

            if( $array_accessor = $this->is_array_accessor( $value_writter ) ) {
                $value_accessor = Create::a( Array_Accessor::class )->with( $array_accessor );
            }
        }

        if( is_a( $value_writter, \Closure::class ) ) {
            $value_accessor = Create::a( Closure_Accessor::class )->with( $value_writter );
        }

        if( $value_accessor === null ) {
            $this->raise_unexpected_write_with_value_error( $value_writter );
        }

        $this->current_field_mapping->set_value_writter( $value_accessor );

        return $this;
    }

    protected function is_method_accessor($accessor)
    {
        $matches = [];

        if( ! \preg_match( "/^(.*)\(.*\)$/", $accessor, $matches ) ) {
            return null;
        }

        return $matches[ 1 ];
    }

    protected function is_property_accessor($accessor)
    {
        $matches = [];

        if( ! \preg_match( "/^->(.*)$/", $accessor, $matches ) ) {
            return null;
        }

        return $matches[ 1 ];
    }

    protected function is_array_accessor($accessor)
    {
        $matches = [];

        if( ! \preg_match( "/^\[(.*)\]$/", $accessor, $matches ) ) {
            return null;
        }

        return $matches[ 1 ];
    }

    /// Raising errors

    protected function raise_unexpected_definition_error($attribute_name)
    {
        throw Create::a( \RuntimeException::class )
                ->with( "Unexpected definition '{$attribute_name}'." );
    }

    protected function raise_unexpected_instantiator_error()
    {
        throw Create::a( \RuntimeException::class )
                ->with( "Unexpected instantiator." );
    }

    protected function raise_unexpected_read_with_value_error($value_reader)
    {
        throw Create::a( \RuntimeException::class )
                ->with( "The read_with value '$value_reader' is not defined. Should be one of '->value', '[value]' or 'value()'." );
    }

    protected function raise_unexpected_write_with_value_error($value_writter)
    {
        throw Create::a( \RuntimeException::class )
                ->with( "The write_with value '$value_writter' is not defined. Should be one of '->value', '[value]' or 'value()'." );
    }
}