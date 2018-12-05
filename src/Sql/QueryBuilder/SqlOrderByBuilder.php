<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\QueryExpressionVisitor;

class SqlOrderByBuilder extends QueryExpressionVisitor
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
    public function accept_order_by_expression($order_by_expression)
    {
        return "order by " . $this->expressions_list(
                $order_by_expression->get_order_by_expressions()
            );
    }
}