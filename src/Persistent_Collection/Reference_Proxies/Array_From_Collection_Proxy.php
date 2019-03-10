<?php

namespace Haijin\Persistency\Persistent_Collection\Reference_Proxies;

use Haijin\Ordered_Collection;

class Array_From_Collection_Proxy extends Reference_Proxy
{
    protected $persistent_collection;
    protected $id_field;

    /// Initializing

    public function __construct(
            $persistent_collection, $id_field,
            $owner_object, $owner_field, $owners_collection, $config
        )
    {
        parent::__construct( $owner_object, $owner_field, $owners_collection, $config );

        $this->persistent_collection = $persistent_collection;
        $this->id_field = $id_field;
    }

    public function fetch_reference()
    {
        $objects = $this->persistent_collection->all( function($query) {

            $query->filter(
                $query
                    ->field( $this->id_field )
                    ->op( "=" )
                    ->value( $this->get_owner_object_id() )
            );

        });

        return new Ordered_Collection( $objects );
    }

    public function resolve_eager_reference_from($objects_space)
    {
        $all_eager_references = $objects_space->get_all_in_collection(
                $this->persistent_collection
            );

        $actual_collection = new Ordered_Collection();

        $owner_object_id = $this->get_owner_object_id();

        $back_id_mapping = $this->persistent_collection->get_primary_key_field_mapping();

        foreach( $all_eager_references as $each_reference ) {

            $back_id = $back_id_mapping->read_value_from( $each_reference );

            if( $owner_object_id == $back_id ) {
                $actual_collection->add( $each_reference );
            }

        }

        $this->resolve_reference_to( $actual_collection );
    }
}