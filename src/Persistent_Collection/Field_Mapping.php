<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;

class Field_Mapping
{
    protected $field_name;
    protected $is_primary_key;
    protected $value_reader;
    protected $value_writter;

    /// Initializing

    public function __construct($field_name)
    {
        $this->field_name = $field_name;
        $this->is_primary_key = false;
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

    public function set_value_reader($value_reader)
    {
        $this->value_reader = $value_reader;
    }

    public function set_value_writter($value_writter)
    {
        $this->value_writter = $value_writter;
    }

    /// Field values

    public function get_mapped_value($raw_record)
    {
        return $raw_record[ $this->field_name ];
    }

    /**
     * Reads the value from the $object.
     */
    public function read_value_from($object)
    {
        if( $this->value_writter === null ) {
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

        $this->value_writter->write_value_to( $object, $value, $mapped_record, $raw_record );
    }
}