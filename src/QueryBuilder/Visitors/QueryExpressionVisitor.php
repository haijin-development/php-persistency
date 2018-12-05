<?php

namespace Haijin\Persistency\QueryBuilder\Visitors;

abstract class QueryExpressionVisitor
{
    /// Visiting

    public function visit($expression)
    {
        return $expression->accept_visitor( $this );
    }

    /// Query expressions

    /**
     * Accepts a QueryExpression.
     */
    abstract public function accept_query_expression($query_expression);

    /**
     * Accepts a CollectionExpression.
     */
    abstract public function accept_collection_expression($collection_expression);

    /**
     * Accepts a ProyectionExpression.
     */
    abstract public function accept_proyection_expression($proyection_expression);

    /**
     * Accepts a JoinExpression.
     */
    abstract public function accept_join_expression($join_expression);


    /**
     * Accepts a FilterExpression.
     */
    abstract public function accept_filter_expression($filter_expression);

    /**
     * Accepts a OrderByExpression.
     */
    abstract public function accept_order_by_expression($order_by_expression);

    /**
     * Accepts a PaginationExpression.
     */
    abstract public function accept_pagination_expression($pagination_expression);

    /// General expressions

    /**
     * Accepts an AllFieldsExpression.
     */
    abstract public function accept_all_fields_expression($all_fields_expression);

    /**
     * Accepts a FieldExpression.
     */
    abstract public function accept_field_expression($field_expression);

    /**
     * Accepts a ValueExpression.
     */
    abstract public function accept_value($value_expression);

    /**
     * Accepts a AliasExpression.
     */
    abstract public function accept_alias_expression($alias_expression);

    /**
     * Accepts a FunctionCallExpression.
     */
    abstract public function accept_function_call_expression($function_call_expression);

    /**
     * Accepts a BinaryOperatorExpression.
     */
    abstract public function accept_binary_operator_expression($function_call_expression);

    /**
     * Accepts a BracketsExpression.
     */
    abstract public function accept_brackets_expression($brackets_expression);
}