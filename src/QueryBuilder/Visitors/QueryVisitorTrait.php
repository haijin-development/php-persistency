<?php

namespace Haijin\Persistency\QueryBuilder\Visitors;

/**
 * A Trait the AbstractQueryExpressionVisitors may use to have a default null implementation
 * on each node acceptance.
 */
trait QueryVisitorTrait
{
    /// Visiting

    /**
     * Accepts a QueryExpression.
     */
    public function accept_query_expression($query_expression)
    {
        $this->raise_unexpected_expression_error( $query_expression );
    }

    /**
     * Accepts a CollectionExpression.
     */
    public function accept_collection_expression($collection_expression)
    {
        $this->raise_unexpected_expression_error( $collection_expression );
    }

    /**
     * Accepts a ProyectionExpression.
     */
    public function accept_proyection_expression($proyection_expression)
    {
        $this->raise_unexpected_expression_error( $proyection_expression );
    }

    /**
     * Accepts a JoinExpression.
     */
    public function accept_join_expression($join_expression)
    {
        $this->raise_unexpected_expression_error( $join_expression );
    }

    /**
     * Accepts a FilterExpression.
     */
    public function accept_filter_expression($filter_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a OrderByExpression.
     */
    public function accept_order_by_expression($order_by_expression)
    {
        $this->raise_unexpected_expression_error( $order_by_expression );
    }

    /**
     * Accepts a PaginationExpression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        $this->raise_unexpected_expression_error( $pagination_expression );
    }

    /**
     * Accepts a AliasExpression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $this->raise_unexpected_expression_error( $alias_expression );
    }

    /**
     * Accepts an AllFieldsExpression.
     */
    public function accept_all_fields_expression($all_fields_expression)
    {
        $this->raise_unexpected_expression_error( $all_fields_expression );
    }

    /**
     * Accepts a FieldExpression.
     */
    public function accept_field_expression($field_expression)
    {
        $this->raise_unexpected_expression_error( $field_expression );
    }

    /**
     * Accepts a ValueExpression.
     */
    public function accept_value($value_expression)
    {
        $this->raise_unexpected_expression_error( $value_expression );
    }

    /**
     * Accepts a FunctionCallExpression.
     */
    public function accept_function_call_expression($function_call_expression)
    {
        $this->raise_unexpected_expression_error( $function_call_expression );
    }

    /**
     * Accepts a BinaryOperatorExpression.
     */
    public function accept_binary_operator_expression($function_call_expression)
    {
        $this->raise_unexpected_expression_error( $function_call_expression );
    }

    /**
     * Accepts a BracketsExpression.
     */
    public function accept_brackets_expression($brackets_expression)
    {
        $this->raise_unexpected_expression_error( $brackets_expression );
    }
}