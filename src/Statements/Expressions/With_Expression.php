<?php

namespace Haijin\Persistency\Statements\Expressions;

class With_Expression extends Join_Expression
{
    protected $joined_field_mapping;

    /// Initializing

    public function __construct(
            $expression_context, $from_collection, $joined_field_mapping
        )
    {
        $this->joined_field_mapping = $joined_field_mapping;

        $referenced_collection_name = $joined_field_mapping->get_type()
            ->get_referenced_collection()->get_collection_name();

        parent::__construct(
            $expression_context,
            $from_collection,
            $this->new_collection_expression( $referenced_collection_name )
        );
    }

    /// Accessing

    public function get_joined_field_mapping()
    {
        return $this->joined_field_mapping;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_with_expression( $this );
    }

    public function build_join_expression_with($sql_builder, $with_expression)
    {
        return $this->joined_field_mapping->get_type()
                ->build_join_expression_with( $sql_builder, $this );
    }
}
