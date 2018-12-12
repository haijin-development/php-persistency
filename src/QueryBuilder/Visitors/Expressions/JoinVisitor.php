<?php

namespace Haijin\Persistency\QueryBuilder\Visitors\Expressions;

use Haijin\Persistency\QueryBuilder\Visitors\AbstractQueryExpressionVisitor;
use Haijin\Persistency\QueryBuilder\Visitors\QueryVisitorTrait;

class JoinVisitor extends AbstractQueryExpressionVisitor
{
    use QueryVisitorTrait;

    /**
     * Accepts a JoinExpression.
     */
    public function accept_join_expression($join_expression)
    {
        $this->raise_unexpected_expression_error( $join_expression );
    }
}