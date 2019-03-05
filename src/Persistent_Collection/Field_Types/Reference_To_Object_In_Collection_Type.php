<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

use Haijin\Persistency\Reference_Proxies\Object_In_Collection_Proxy;

class Reference_To_Object_In_Collection_Type
{
    protected $persistent_collection;

    /// Initializing

    public function __construct($persistent_collection)
    {
        $this->persistent_collection = $persistent_collection;
    }

    public function convert_to_database($object, $database)
    {
        return $this->persistent_collection::get()->get_id_of( $object );
    }

    public function convert_from_database(
            $raw_record, $field_name, $database, $object, $object_id, $value_writter
        )
    {
        $object_id = $raw_record[ $field_name ];

        if( $object_id === null ) {
            return null;
        }

        return new Object_In_Collection_Proxy(
            $this->persistent_collection,
            $object_id,
            $object,
            $value_writter
        );
    }
}