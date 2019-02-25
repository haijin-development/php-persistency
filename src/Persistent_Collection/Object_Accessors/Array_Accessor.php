<?php

namespace Haijin\Persistency\Persistent_Collection\Object_Accessors;

use Haijin\Instantiator\Create;

class Array_Accessor
{
    protected $key;

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * Read value from the $object.
     */
    public function read_value_from($object)
    {
        $key = $this->key;

        return $object[ $key ];
    }

    /**
     * Writtes the value to the $object.
     */
    public function write_value_to(&$object, $value)
    {
        $key = $this->key;

        $object[ $key ] = $value ;
    }
}