<?php

namespace Haijin\Persistency\Persistent_Collection\Field_Types;

use Haijin\Persistency\Errors\Query_Expressions\Invalid_Expression_Error;

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

    /// Accessing

    public function get_referenced_collection()
    {
        return null;
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

    /// Joining double dispatch

    public function join_from($query_expression, $field_mapping)
    {
        throw new Invalid_Expression_Error(
            "Can not use '\$query->with()' with a value field."
        );
    }

    /// Double dispatch - Building html

    public function build_join_expression_with($sql_builder, $with_expression)
    {
        return null;
    }
}