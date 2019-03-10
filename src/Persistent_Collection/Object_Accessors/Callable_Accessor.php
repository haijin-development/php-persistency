<?php

namespace Haijin\Persistency\Persistent_Collection\Object_Accessors;

use Haijin\Instantiator\Create;

class Callable_Accessor
{
    protected $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    /**
     * Read value from the $object.
     */
    public function read_value_from($object)
    {
        return ( $this->callable )( $object );
    }

    /**
     * Writtes the value to the $object.
     */
    public function write_value_to($object, $value, $mapped_record, $raw_record)
    {
        ( $this->callable )( $object, $mapped_record, $raw_record );
    }
}