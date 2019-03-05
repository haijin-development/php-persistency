<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

use Haijin\Persistency\Persistent_Collection\Reference_Proxies\Object_In_Collection_Proxy;

class Reference_To_Object_In_Collection_Type extends Abstract_Type
{
    protected $persistent_collection;
    protected $config;

    /// Initializing

    public function __construct($persistent_collection, $config)
    {
        $this->persistent_collection = $persistent_collection;
        $this->config = $config;
    }

    /// Asking

    public function references_other_collection()
    {
        return true;
    }

    public function get_referenced_collection()
    {
        return $this->persistent_collection;
    }

    /// Converting

    public function convert_to_database($object, $database)
    {
        return $this->persistent_collection::get()->get_id_of( $object );
    }

    public function convert_from_database(
            $raw_record, $owner_object, $owner_field, $owners_collection, $database
        )
    {
        if( ! isset( $raw_record[ $owner_field ] ) ) {
            return null;
        }

        $object_id = $raw_record[ $owner_field ];

        if( $object_id === null ) {
            return null;
        }

        return new Object_In_Collection_Proxy(
            $this->persistent_collection,
            $object_id,
            $owner_object,
            $owner_field,
            $owners_collection,
            $this->config
        );
    }

    public function fetch_actual_refereces_from_all($proxies)
    {
        $ids = [];


        foreach( $proxies as $proxy ) {

            if( $proxy !== null ) {
                $ids[] = $proxy->get_object_id();
            }
        }

        if( empty( $ids ) ) {
            return [];
        }

        $collection_id = $this->persistent_collection::do()->get_id_field();

        $objects = $this->persistent_collection::get()->all(
                                        function($query) use($collection_id, $ids) {

            $query->filter(
                $query->field( $collection_id ) ->in( $ids )
            );

        });

        return $objects;
    }
}