<?php

namespace Haijin\Persistency\QueryBuilder\Visitors\Expressions;

use Haijin\Persistency\QueryBuilder\Visitors\AbstractQueryExpressionVisitor;
use Haijin\Persistency\QueryBuilder\Visitors\QueryVisitorTrait;

class FilterVisitor extends AbstractQueryExpressionVisitor
{
    use QueryVisitorTrait;

    /**
     * Accepts a FilterExpression.
     */
    public function accept_filter_expression($filter_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }
}