<?php

namespace Haijin\Persistency\Persistent_Collection\Object_Accessors;

use Haijin\Instantiator\Create;

class Method_Accessor
{
    protected $method_name;

    public function __construct($method_name)
    {
        $this->method_name = $method_name;
    }

    /**
     * Read value from the $object.
     */
    public function read_value_from($object)
    {
        $method_name = $this->method_name;

        return $object->$method_name();
    }

    /**
     * Writtes the value to the $object.
     */
    public function write_value_to($object, $value)
    {
        $method_name = $this->method_name;

        $object->$method_name( $value );
    }
}