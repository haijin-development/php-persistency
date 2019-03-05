<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

use Haijin\Persistency\Persistent_Collection\Reference_Proxies\Array_Through_Collection_Proxy;

class Reference_Collection_Through_Collection_Type extends Abstract_Type
{
    protected $middle_table;
    protected $left_id_field;
    protected $right_id_field;
    protected $other_persistent_collection;
    protected $config;

    /// Initializing

    public function __construct(
            $middle_table, $left_id_field, $right_id_field, $other_persistent_collection,
            $config
        )
    {
        $this->middle_table = $middle_table;
        $this->left_id_field = $left_id_field;
        $this->right_id_field = $right_id_field;
        $this->other_persistent_collection = $other_persistent_collection;
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
        return $this->other_persistent_collection;
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
        return new Array_Through_Collection_Proxy(
            $this->middle_table,
            $this->left_id_field,
            $this->right_id_field,
            $this->other_persistent_collection,
            $owner_object,
            $owner_field,
            $owners_collection,
            $this->config
        );
    }
}