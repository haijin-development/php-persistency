<?php

namespace Haijin\Persistency\Statements_Visitors\Expressions;

use Haijin\Persistency\Statements_Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Statements_Visitors\Query_Visitor_Trait;

class Expression_Visitor extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;

    /// Visiting

    /**
     * Accepts a Count_Expression.
     */
    public function accept_count_expression($count_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts an All_Fields_Expression.
     */
    public function accept_all_fields_expression($all_fields_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a Field_Expression.
     */
    public function accept_field_expression($field_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a Value_Expression.
     */
    public function accept_value_expression($value_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a Alias_Expression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a Function_Call_Expression.
     */
    public function accept_function_call_expression($function_call_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a Binary_Operator_Expression.
     */
    public function accept_binary_operator_expression($binary_operator_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a Brackets_Expression.
     */
    public function accept_brackets_expression($brackets_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }
}