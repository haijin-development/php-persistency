<?php

namespace Haijin\Persistency\QueryBuilder\Visitors\Expressions;

use Haijin\Persistency\QueryBuilder\Visitors\AbstractQueryExpressionVisitor;
use Haijin\Persistency\QueryBuilder\Visitors\QueryVisitorTrait;

class PaginationVisitor extends AbstractQueryExpressionVisitor
{
    use QueryVisitorTrait;

    /**
     * Accepts a PaginationExpression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        $this->raise_unexpected_expression_error( $pagination_expression );
    }
}