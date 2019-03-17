<?php

namespace Haijin\Persistency\Statements_Visitors;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Errors\Query_Expressions\Unexpected_Expression_Error;


class Expression_Visitor extends Abstract_Expression_Visitor
{
    /// Visiting

    /**
     * Accepts a Query_Statement.
     */
    public function accept_query_statement($query_statement)
    {
        $this->raise_unexpected_expression_error( $query_statement );
    }

    /**
     * Accepts a Create_Statement.
     */
    public function accept_create_statement($create_statement)
    {
        $this->raise_unexpected_expression_error( $create_statement );
    }

    /**
     * Accepts an Update_Statement.
     */
    public function accept_update_statement($update_statement)
    {
        $this->raise_unexpected_expression_error( $update_statement );
    }

    /**
     * Accepts an Delete_Statement.
     */
    public function accept_delete_statement($delete_statement)
    {
        $this->raise_unexpected_expression_error( $delete_statement );
    }

    /**
     * Accepts a Collection_Expression.
     */
    public function accept_collection_expression($collection_expression)
    {
        $this->raise_unexpected_expression_error( $collection_expression );
    }

    /**
     * Accepts a Proyection_Expression.
     */
    public function accept_proyection_expression($proyection_expression)
    {
        $this->raise_unexpected_expression_error( $proyection_expression );
    }

    /**
     * Accepts a Join_Expression.
     */
    public function accept_join_expression($join_expression)
    {
        $this->raise_unexpected_expression_error( $join_expression );
    }

    /**
     * Accepts a Filter_Expression.
     */
    public function accept_filter_expression($filter_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a Having_Expression.
     */
    public function accept_having_expression($having_expression)
    {
        $this->raise_unexpected_expression_error( $having_expression );
    }

    /**
     * Accepts a Group_By_Expression.
     */
    public function accept_group_by_expression($group_by_expression)
    {
        $this->raise_unexpected_expression_error( $group_by_expression );
    }

    /**
     * Accepts a Order_By_Expression.
     */
    public function accept_order_by_expression($order_by_expression)
    {
        $this->raise_unexpected_expression_error( $order_by_expression );
    }

    /**
     * Accepts a Pagination_Expression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        $this->raise_unexpected_expression_error( $pagination_expression );
    }

    /**
     * Accepts a Alias_Expression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $this->raise_unexpected_expression_error( $alias_expression );
    }

    /**
     * Accepts a Count_Expression.
     */
    public function accept_count_expression($count_expression)
    {
        $this->raise_unexpected_expression_error( $count_expression );
    }

    /**
     * Accepts an All_Fields_Expression.
     */
    public function accept_all_fields_expression($all_fields_expression)
    {
        $this->raise_unexpected_expression_error( $all_fields_expression );
    }

    /**
     * Accepts a Field_Expression.
     */
    public function accept_field_expression($field_expression)
    {
        $this->raise_unexpected_expression_error( $field_expression );
    }

    /**
     * Accepts a Value_Expression.
     */
    public function accept_value_expression($value_expression)
    {
        $this->raise_unexpected_expression_error( $value_expression );
    }

    /**
     * Accepts a Named_Parameter_Expression.
     */
    public function accept_named_parameter_expression($named_parameter_expression)
    {
        $this->raise_unexpected_expression_error( $named_parameter_expression );
    }

    /**
     * Accepts a Function_Call_Expression.
     */
    public function accept_function_call_expression($function_call_expression)
    {
        $this->raise_unexpected_expression_error( $function_call_expression );
    }

    /**
     * Accepts a Binary_Operator_Expression.
     */
    public function accept_binary_operator_expression($function_call_expression)
    {
        $this->raise_unexpected_expression_error( $function_call_expression );
    }

    /**
     * Accepts a Brackets_Expression.
     */
    public function accept_brackets_expression($brackets_expression)
    {
        $this->raise_unexpected_expression_error( $brackets_expression );
    }

    /**
     * Accepts a Record_Values_Expression.
     */
    public function accept_record_values_expression($record_values_expression)
    {
        $this->raise_unexpected_expression_error( $record_values_expression );
    }

    /// Raising errors

    protected function raise_unexpected_expression_error($expression)
    {
        $expression_name = get_class( $expression );

        throw new Unexpected_Expression_Error(
            "Unexpected {$expression_name}",
            $expression
        );
    }
}