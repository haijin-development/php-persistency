<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\Expressions\FilterVisitor;

class SqlFilterBuilder extends FilterVisitor
{
    use SqlBuilderTrait;

    /// Visiting

    /**
     * Accepts a OrderByExpression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return "where " . $this->expression_sql_from( $filter_expression->get_filter() );
    }
}