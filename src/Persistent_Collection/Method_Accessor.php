<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;

class Method_Accessor
{
    protected $method_name;

    public function __construct($method_name)
    {
        $this->method_name = $method_name;
    }

    /**
     * Writtes the value from the $record to the $object.
     * It receives the whole record instead of just a single attribute value
     * since the convertion between field attributes and object properties may involve
     * more than one record attribute, for instance if it contains a value and a unit, such
     * as 10 'kb'.
     */
    public function write_value_to($object, $record, $field_name)
    {
        $method_name = $this->method_name;

        $object->$method_name( $record[ $field_name ] );
    }
}