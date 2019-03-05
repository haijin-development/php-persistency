<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

use Haijin\Persistency\Persistent_Collection\Reference_Proxies\Object_From_Collection_Proxy;

class Reference_From_Object_In_Collection_Type extends Abstract_Type
{
    protected $persistent_collection;
    protected $id_field;
    protected $config;

    /// Initializing

    public function __construct($persistent_collection, $id_field, $config)
    {
        $this->persistent_collection = $persistent_collection;
        $this->id_field = $id_field;
        $this->config = $config;
    }

    /// Asking

    public function references_other_collection()
    {
        return true;
    }

    public function can_write_to_database()
    {
        return false;
    }

    public function get_referenced_collection()
    {
        return $this->persistent_collection;
    }

    /// Converting

    public function convert_to_database($object, $database)
    {
        throw new \RuntimeException( "This type is not written into the database." );
    }

    public function convert_from_database(
            $raw_record, $owner_object, $owner_field, $owners_collection, $database
        )
    {
        return new Object_From_Collection_Proxy(
            $this->persistent_collection,
            $this->id_field,
            $owner_object,
            $owner_field,
            $owners_collection,
            $this->config
        );
    }
}