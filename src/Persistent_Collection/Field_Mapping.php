<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Types_Converters\Types_Converter;

class Field_Mapping
{
    protected $field_name;
    protected $is_primary_key;
    protected $type;
    protected $value_reader;
    protected $value_writter;

    /// Initializing

    public function __construct($field_name)
    {
        $this->field_name = $field_name;
        $this->is_primary_key = false;
        $this->type = null;
        $this->value_reader = null;
        $this->value_writter = null;
    }

    /// Accessing

    public function get_field_name()
    {
        return $this->field_name;
    }

    public function set_field_name($field_name)
    {
        $this->field_name = $field_name;
    }

    public function is_primary_key()
    {
        return $this->is_primary_key;
    }

    public function set_is_primary_key($boolean)
    {
        $this->is_primary_key = $boolean;
    }

    public function get_type()
    {
        return $this->type;
    }

    public function set_type($type)
    {
        $this->type = $type;
    }

    public function set_value_reader($value_reader)
    {
        $this->value_reader = $value_reader;
    }

    public function get_value_reader()
    {
        return $this->value_reader;
    }

    public function set_value_writter($value_writter)
    {
        $this->value_writter = $value_writter;
    }

    public function get_value_writter()
    {
        return $this->value_writter;
    }

    /// Asking

    public function reads_from_object()
    {
        return $this->value_reader !== null;
    }

    /// Field values

    public function convert_value_from_db(
            $raw_record, $owner_object, $owner_field, $owners_collection, $database
        )
    {
        if( $this->type === null ) {
            return isset( $raw_record[ $owner_field ] ) ? 
                $raw_record[ $owner_field ] : null;
        }

        return $this->type->convert_from_database(
            $raw_record,
            $owner_object,
            $owner_field,
            $owners_collection,
            $database
        );
    }

    public function convert_value_to_db($value, $database)
    {
        if( $this->type === null || $value === null ) {
            return $value;
        }

        return $this->type->convert_to_database( $value, $database );
    }

    /**
     * Reads the value from the $object.
     */
    public function read_value_from($object)
    {
        if( $this->value_reader === null ) {
            throw new \RuntimeException( "Field mapping '{$this->field_name}' is missing the object value reader in its definition." );
        }

        return $this->value_reader->read_value_from( $object );
    }

    /**
     * Writtes the value to the $object.
     */
    public function write_value_to(&$object, $value, $mapped_record, $raw_record)
    {
        if( $this->value_writter === null ) {
            return;
        }

        $this->value_writter
                ->write_value_to( $object, $value, $mapped_record, $raw_record );
    }
}