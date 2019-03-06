<?php

namespace Haijin\Persistency\Persistent_Collection\Reference_Proxies;

class Object_In_Collection_Proxy extends Reference_Proxy
{
    protected $persistent_collection;
    protected $object_id;

    /// Initializing

    public function __construct(
            $persistent_collection, $object_id,
            $owner_object, $owner_field, $owners_collection,
            $config
        )
    {
        parent::__construct( $owner_object, $owner_field, $owners_collection, $config );

        $this->persistent_collection = $persistent_collection;
        $this->object_id = $object_id;
    }

    /// Accessing

    public function get_object_id()
    {
        return $this->object_id;
    }

    /// Resolving reference

    public function fetch_reference()
    {
        return $this->persistent_collection->find_by_id( $this->object_id );
    }

    public function resolve_eager_reference_from($objects_space)
    {
        $actual_reference = $objects_space->get_object_by_id(
            $this->persistent_collection,
            $this->object_id
        );

        $this->resolve_reference_to( $actual_reference );
    }
}