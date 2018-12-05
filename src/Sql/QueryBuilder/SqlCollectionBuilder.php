<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\QueryExpressionVisitor;

class SqlCollectionBuilder extends QueryExpressionVisitor
{
    use SqlBuilderTrait;

    /// Visiting

    /**
     * Accepts a CollectionExpression.
     */
    public function accept_collection_expression($collection_expression)
    {
        return "from " . $this->escape(
                $collection_expression->get_collection_name()
            );
    }
}