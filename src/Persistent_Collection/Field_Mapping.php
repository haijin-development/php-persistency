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

    public function set_value_reader($value_reader)
    {
        $this->value_reader = $value_reader;
    }

    public function set_value_writter($value_writter)
    {
        $this->value_writter = $value_writter;
    }

    /// Field values

    /**
     * Writtes the value from the $record to the $object.
     * It receives the whole record instead of just a single attribute value
     * since the convertion between field attributes and object properties may involve
     * more than one record attribute, for instance if it contains a value and a unit, such
     * as 10 'kb'.
     */
    public function write_value_to( $object, $record )
    {
        $this->value_writter->write_value_to( $object, $record, $this->field_name );
    }
}