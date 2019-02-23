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
     * Writtes the value to the $object.
     */
    public function write_value_to($object, $value)
    {
        $method_name = $this->method_name;

        $object->$method_name( $value );
    }
}