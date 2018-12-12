<?php

namespace Haijin\Persistency\QueryBuilder\Visitors\Expressions;

use Haijin\Persistency\QueryBuilder\Visitors\AbstractQueryExpressionVisitor;
use Haijin\Persistency\QueryBuilder\Visitors\QueryVisitorTrait;

class ProyectionVisitor extends AbstractQueryExpressionVisitor
{
    use QueryVisitorTrait;

    /**
     * Accepts a ProyectionExpression.
     */
    public function accept_proyection_expression($proyection_expression)
    {
        $this->raise_unexpected_expression_error( $proyection_expression );
    }
}