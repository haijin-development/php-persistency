<?php

namespace Haijin\Persistency\Persistent_Collection\Reference_Proxies;

class Object_From_Collection_Proxy extends Reference_Proxy
{
    protected $persistent_collection;
    protected $id_field;

    /// Initializing

    public function __construct(
            $persistent_collection, $id_field,
            $owner_object, $owner_field, $owners_collection,
            $config
        )
    {
        parent::__construct( $owner_object, $owner_field, $owners_collection, $config );

        $this->persistent_collection = $persistent_collection;
        $this->id_field = $id_field;
    }

    /// Acessing

    public function get_persistent_collection()
    {
        return $this->persistent_collection;
    }

    public function get_id_field()
    {
        return $this->id_field;
    }

    /// Resolving reference

    public function fetch_reference()
    {
        return $this->persistent_collection->find_by([
            $this->id_field => $this->get_owner_object_id()
        ]);
    }

    public function resolve_eager_reference_from($objects_space)
    {
        $all_eager_references = $objects_space->get_all_in_collection(
                $this->persistent_collection
            );

        $actual_object = null;

        $owner_object_id = $this->get_owner_object_id();

        $back_id_mapping = $this->persistent_collection->get_primary_key_field_mapping();

        foreach( $all_eager_references as $each_reference ) {

            $back_id = $back_id_mapping->read_value_from( $each_reference );

            if( $owner_object_id == $back_id ) {
                $actual_object = $each_reference;
                break;
            }

        }

        $this->resolve_reference_to( $actual_object );
    }
}