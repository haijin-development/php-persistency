<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Instantiator\Create;

class Property_Accessor
{
    protected $property_name;

    public function __construct($property_name)
    {
        $this->property_name = $property_name;
    }

    /**
     * Read value from the $object.
     */
    public function read_value_from($object)
    {
        $property_name = $this->property_name;

        return $object->$property_name;
    }

    /**
     * Writtes the value to the $object.
     */
    public function write_value_to($object, $value)
    {
        $property_name = $this->property_name;

        $object->$property_name = $value;
    }
}