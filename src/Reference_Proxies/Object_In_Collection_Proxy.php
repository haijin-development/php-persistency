<?php

namespace Haijin\Persistency\Reference_Proxies;

class Object_In_Collection_Proxy
{
    protected $persistent_collection;
    protected $object_id;
    protected $owner_object;
    protected $owner_object_value_writter;

    /// Initializing

    public function __construct($persistent_collection, $object_id,
        $owner_object, $owner_object_value_writter)
    {
        $this->persistent_collection = $persistent_collection;
        $this->object_id = $object_id;
        $this->owner_object = $owner_object;
        $this->owner_object_value_writter = $owner_object_value_writter;
    }

    public function fetch_reference()
    {
        return $this->persistent_collection::do()
                    ->find_by_id( $this->object_id );
    }

    public function resolve_reference()
    {
        $fetched_reference = $this->fetch_reference();

        $this->owner_object_value_writter->write_value_to(
            $this->owner_object,
            $fetched_reference
        );

        return $fetched_reference;
    }

    /// Proxy methods

    public function __call($method_name, $params)
    {
        return $this->resolve_reference()->$method_name( ...$params );
    }

    public function __set($property_name, $value)
    {
        return $this->resolve_reference()->$property_name = $value;
    }

    public function __get($property_name)
    {
        return $this->resolve_reference()->$property_name;
    }
}