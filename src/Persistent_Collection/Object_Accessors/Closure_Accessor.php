<?php

namespace Haijin\Persistency\Persistent_Collection\Object_Accessors;

use Haijin\Instantiator\Create;

class Closure_Accessor
{
    protected $closure;

    public function __construct($closure)
    {
        $this->closure = $closure;
    }

    /**
     * Read value from the $object.
     */
    public function read_value_from($object)
    {
        return $this->closure->call( $this, $object );
    }

    /**
     * Writtes the value to the $object.
     */
    public function write_value_to($object, $value, $mapped_record, $raw_record)
    {
        $this->closure->call($this, $object, $mapped_record, $raw_record );
    }
}