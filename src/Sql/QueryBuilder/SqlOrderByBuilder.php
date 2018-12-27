<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\Factory\Factory;
use Haijin\Persistency\QueryBuilder\Visitors\Expressions\OrderByVisitor;
use Haijin\Persistency\Sql\QueryBuilder\ExpressionBuilders\SqlExpressionInOrderByBuilder;

class SqlOrderByBuilder extends OrderByVisitor
{
    use SqlBuilderTrait;

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

    protected function new_sql_expression_builder()
    {
        return Factory::new( SqlExpressionInOrderByBuilder::class, false );
    }
}