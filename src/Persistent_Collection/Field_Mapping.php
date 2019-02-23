<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;

class Field_Mapping
{
    protected $field_name;
    protected $value_reader;
    protected $value_writter;

    /// Initializing

    public function __construct($field_name)
    {
        $this->field_name = $field_name;

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
     * Writtes the value to the $object.
     */
    public function write_value_to($object, $value)
    {
        if( $this->value_writter === null ) {
            return;
        }

        $this->value_writter->write_value_to( $object, $value );
    }
}