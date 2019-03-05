<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

class Value_Type extends Abstract_Type
{
    protected $type;

    /// Initializing

    public function __construct($type)
    {
        $this->type = $type;
    }

    /// Asking

    public function references_other_collection()
    {
        return false;
    }

    /// Converting

    public function convert_from_database(
            $raw_record, $owner_object, $owner_field, $owners_collection, $database
        )
    {
        if( ! isset( $raw_record[ $owner_field ] ) ) {
            return null;
        }

        $value = $raw_record[ $owner_field ];

        if( $value === null ) {
            return null;
        }

        return $database->get_types_converter()
            ->convert_from_database( $this->type, $value );
    }

    public function convert_to_database($value, $database)
    {
        return $value;
    }
}