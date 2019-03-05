<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

use Haijin\Persistency\Reference_Proxies\Object_From_Collection_Proxy;

class Reference_From_Object_In_Collection_Type
{
    protected $persistent_collection;
    protected $id_field;

    /// Initializing

    public function __construct($persistent_collection, $id_field)
    {
        $this->persistent_collection = $persistent_collection;
        $this->id_field = $id_field;
    }

    public function convert_to_database($object, $database)
    {
        throw new \RuntimeException( "This type is not written into the database." );
    }

    public function convert_from_database(
            $raw_record, $field_name, $database, $object, $object_id, $value_writter
        )
    {
        return new Object_From_Collection_Proxy(
            $this->persistent_collection,
            $this->id_field,
            $object_id,
            $object,
            $value_writter
        );
    }
}