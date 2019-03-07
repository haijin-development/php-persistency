<?php

namespace Haijin\Persistency\Statements_Visitors;

abstract class Abstract_Expression_Visitor
{
    /// Visiting

    public function visit($expression)
    {
        return $expression->accept_visitor( $this );
    }

    /// Query expressions

    /**
     * Accepts a Query_Statement.
     */
    abstract public function accept_query_statement($query_statement);

    /**
     * Accepts a Create_Statement.
     */
    abstract public function accept_create_statement($create_statement);

    /**
     * Accepts an Update_Statement.
     */
    abstract public function accept_update_statement($update_statement);

    /**
     * Accepts an Delete_Statement.
     */
    abstract public function accept_delete_statement($delete_statement);

    /**
     * Accepts a Collection_Expression.
     */
    abstract public function accept_collection_expression($collection_expression);

    /**
     * Accepts a Proyection_Expression.
     */
    abstract public function accept_proyection_expression($proyection_expression);

    /**
     * Accepts a Join_Expression.
     */
    abstract public function accept_join_expression($join_expression);


    /**
     * Accepts a Filter_Expression.
     */
    abstract public function accept_filter_expression($filter_expression);

    /**
     * Accepts a Group_By_Expression.
     */
    abstract public function accept_group_by_expression($group_by_expression);

    /**
     * Accepts a Order_By_Expression.
     */
    abstract public function accept_order_by_expression($order_by_expression);

    /**
     * Accepts a Pagination_Expression.
     */
    abstract public function accept_pagination_expression($pagination_expression);

    /// General expressions

    /**
     * Accepts a Count_Expression.
     */
    abstract public function accept_count_expression($count_expression);

    /**
     * Accepts an All_Fields_Expression.
     */
    abstract public function accept_all_fields_expression($all_fields_expression);

    /**
     * Accepts a Field_Expression.
     */
    abstract public function accept_field_expression($field_expression);

    /**
     * Accepts a Value_Expression.
     */
    abstract public function accept_value_expression($value_expression);

    /**
     * Accepts a Named_Parameter_Expression.
     */
    abstract public function accept_named_parameter_expression($named_parameter_expression);

    /**
     * Accepts a Alias_Expression.
     */
    abstract public function accept_alias_expression($alias_expression);

    /**
     * Accepts a Function_Call_Expression.
     */
    abstract public function accept_function_call_expression($function_call_expression);

    /**
     * Accepts a Binary_Operator_Expression.
     */
    abstract public function accept_binary_operator_expression($function_call_expression);

    /**
     * Accepts a Brackets_Expression.
     */
    abstract public function accept_brackets_expression($brackets_expression);

    /**
     * Accepts a Record_Values_Expression.
     */
    abstract public function accept_record_values_expression($record_values_expression);
}