<?php

namespace Haijin\Persistency\QueryBuilder\Visitors\Expressions;

use Haijin\Persistency\QueryBuilder\Visitors\AbstractQueryExpressionVisitor;
use Haijin\Persistency\QueryBuilder\Visitors\QueryVisitorTrait;

class CollectionVisitor extends AbstractQueryExpressionVisitor
{
    use QueryVisitorTrait;

    /**
     * Accepts a CollectionExpression.
     */
    public function accept_collection_expression($collection_expression)
    {
        $this->raise_unexpected_expression_error( $collection_expression );
    }
}