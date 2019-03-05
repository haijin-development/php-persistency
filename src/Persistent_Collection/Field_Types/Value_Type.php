<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

class Value_Type
{
    protected $type;

    /// Initializing

    public function __construct($type)
    {
        $this->type = $type;
    }

    public function convert_from_database(
            $raw_record, $field_name, $database, $object, $object_id, $value_writter
        )
    {
        $value = $raw_record[ $field_name ];

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