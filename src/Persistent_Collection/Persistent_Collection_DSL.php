<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;

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

    public function read_with($value_reader)
    {
        $this->current_field_mapping->set_value_reader( $value_reader );

        return $this;
    }

    public function write_with($value_writter)
    {
        $value_accessor = null;

        if( is_string( $value_writter) ) {

            if( $method_accessor = $this->is_method_accessor( $value_writter ) ) {
                $value_accessor = Create::a( Method_Accessor::class )->with( $method_accessor );
            }
        }

        $this->current_field_mapping->set_value_writter( $value_accessor );

        return $this;
    }

    protected function is_method_accessor($value_writter)
    {
        $matches = [];

        if( ! \preg_match( "/^(.*)\(.*\)$/", $value_writter, $matches ) ) {
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
}
