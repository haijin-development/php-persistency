<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\QueryExpressionVisitor;

class SqlFilterBuilder extends QueryExpressionVisitor
{
    use SqlBuilderTrait;

    protected $collection;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    /// Visiting

    /**
     * Accepts a OrderByExpression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return "where " . $this->expression_sql_from( $filter_expression->get_filter() );
    }
}