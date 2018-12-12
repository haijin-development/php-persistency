<?php

namespace Haijin\Persistency\QueryBuilder\Visitors\Expressions;

use Haijin\Persistency\QueryBuilder\Visitors\AbstractQueryExpressionVisitor;
use Haijin\Persistency\QueryBuilder\Visitors\QueryVisitorTrait;

class ExpressionVisitor extends AbstractQueryExpressionVisitor
{
    use QueryVisitorTrait;

    /// Visiting

    /**
     * Accepts an AllFieldsExpression.
     */
    public function accept_all_fields_expression($all_fields_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a FieldExpression.
     */
    public function accept_field_expression($field_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a ValueExpression.
     */
    public function accept_value($value_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a AliasExpression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a FunctionCallExpression.
     */
    public function accept_function_call_expression($function_call_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a BinaryOperatorExpression.
     */
    public function accept_binary_operator_expression($binary_operator_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }

    /**
     * Accepts a BracketsExpression.
     */
    public function accept_brackets_expression($brackets_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }
}