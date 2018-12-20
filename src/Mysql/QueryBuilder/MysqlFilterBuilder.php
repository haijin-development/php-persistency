<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilderTrait;
use Haijin\Persistency\QueryBuilder\Visitors\Expressions\FilterVisitor;

class MysqlFilterBuilder extends FilterVisitor
{
    use SqlBuilderTrait;

    protected $query_parameters;

    /// Initializing

    public function __construct($query_parameters)
    {
        $this->query_parameters = $query_parameters;
    }

    /// Visiting

    /**
     * Accepts a OrderByExpression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return "where " . $this->expression_sql_from( $filter_expression->get_filter() );
    }

    protected function new_sql_expression_builder()
    {
        return new MysqlExpressionBuilder( $this->query_parameters );
    }
}