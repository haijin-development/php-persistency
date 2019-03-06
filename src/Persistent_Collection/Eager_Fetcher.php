<?php

namespace Haijin\Persistency\Persistent_Collection;

use Haijin\Ordered_Collection;

class Eager_Fetcher
{
    protected $objects_space;

    /// Initializing

    public function __construct()
    {
        $this->objects_space = new Objects_Space();
    }

    /// Resolving references

    public function resolve_references_in_collection($collection, $objects, $fetch_spec)
    {
        if( empty( $objects ) ) {
            return $objects;
        }

        $this->objects_space->add_all( $collection, $objects );


        $eager_fields = array_keys( $fetch_spec );

        foreach( $eager_fields as $field ) {

            $mapping = $collection->get_field_mapping_at( $field );

            $next_level_eager_fields = $fetch_spec[ $field ];
            if( ! is_array( $next_level_eager_fields ) ) {
                $next_level_eager_fields = [];
            }

            $this->resolve_references_in_field_mapping(
                $mapping,
                $objects, 
                $next_level_eager_fields
            );

        }
    }

    protected function resolve_references_in_field_mapping(
            $mapping, $objects, $next_level_fetch_spec
        )
    {
        if( ! $mapping->references_other_collection() ) {
            return;
        }

        $proxies = $this->collect_proxies_from_all( $mapping, $objects );

        $all_references = $mapping->get_type()
                            ->fetch_actual_refereces_from_all( $proxies );

        $referenced_collection = $mapping->get_referenced_collection();

        $this->objects_space->add_all( $referenced_collection, $all_references );

        foreach( $objects as $object ) {

            $proxy = $mapping->read_value_from( $object );

            if( $proxy === null ) {
                continue;
            }

            $proxy->resolve_eager_reference_from(
                $this->objects_space
            );

        }

        $next_level_objects = $all_references;

        $this->resolve_references_in_collection(
            $referenced_collection,
            $all_references,
            $next_level_fetch_spec
        );
    }

    protected function collect_proxies_from_all($mapping, $objects)
    {
        $proxies = [];

        foreach( $objects as $object ) {

            $value = $mapping->read_value_from( $object );

            if( $value !== null ) {
                $proxies[] = $value;
            }

        }

        return $proxies;
    }
}
