<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Ordered_Collection;

class Objects_Space
{
    protected $objects_by_classes;

    /// Initializing

    public function __construct()
    {
        $this->objects_by_classes = [];
    }

    /// Adding

    public function add_all($collection, $objects)
    {
        foreach( $objects as $object) {
            $this->add( $collection, $object );
        }
    }

    public function add($collection, $object)
    {
        $collection_name = get_class( $collection );

        if( ! isset( $this->objects_by_classes[ $collection_name ] ) ) {
            $this->objects_by_classes[ $collection_name ] = [];
        }

        $object_id = $collection->get_id_of( $object );

        $this->objects_by_classes[ $collection_name ][ $object_id ] = $object;
    }

    /// Searching

    public function get_object_by_id($collection, $object_id)
    {
        $collection_name = get_class( $collection );

        if( ! isset( $this->objects_by_classes[ $collection_name ][ $object_id ] ) ) {
            throw new Haijin_Error( "Object with id {$object_id} from collection {$collection_name} not found in objects space." );
        }

        return $this->objects_by_classes[ $collection_name ][ $object_id ];
    }

    public function get_all_in_collection($collection)
    {
        $collection_name = get_class( $collection );

        if( ! isset( $this->objects_by_classes[ $collection_name ] ) ) {
            return [];
        }

        return $this->objects_by_classes[ $collection_name ];
    }
}
