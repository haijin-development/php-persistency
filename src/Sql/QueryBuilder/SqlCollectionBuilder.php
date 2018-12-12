<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\AbstractQueryExpressionVisitor;
use Haijin\Persistency\QueryBuilder\Visitors\Expressions\CollectionVisitor;

class SqlCollectionBuilder extends CollectionVisitor
{
    use SqlBuilderTrait;

    /// Visiting

    /**
     * Accepts a CollectionExpression.
     */
    public function accept_collection_expression($collection_expression)
    {
        return "from " . $this->escape_sql(
                $collection_expression->get_collection_name()
            );
    }
}