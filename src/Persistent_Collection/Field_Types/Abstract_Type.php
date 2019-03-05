<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

abstract class Abstract_Type
{
    /// Converting

    abstract public function convert_from_database(
            $raw_record, $owner_object, $owner_field, $owners_collection, $database
        );

    abstract public function convert_to_database($value, $database);

    /// Asking

    abstract public function references_other_collection();

    public function can_write_to_database()
    {
        return true;
    }
}