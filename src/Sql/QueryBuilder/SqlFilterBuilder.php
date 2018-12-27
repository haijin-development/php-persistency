<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\Factory\Factory;
use Haijin\Persistency\QueryBuilder\Visitors\Expressions\FilterVisitor;
use Haijin\Persistency\Sql\QueryBuilder\ExpressionBuilders\SqlExpressionInFilterBuilder;

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


    /// Creating instances

    protected function new_sql_expression_builder()
    {
        return Factory::new( SqlExpressionInFilterBuilder::class );
    }
}