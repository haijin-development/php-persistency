<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class MysqlQueryBuilder extends SqlBuilder
{
    protected $query_parameters;

    /// Initializing

    public function __construct($query_parameters)
    {
        $this->query_parameters = $query_parameters;
    }

    /// Accessing

    public function get_parameters()
    {
        return $this->query_parameters->get_parameters();
    }

    /// Visiting

    /**
     * Accepts a PaginationExpression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        return ( new MysqlPaginationBuilder() )
            ->build_sql_from( $pagination_expression );
    }

    /**
     * Accepts a FilterExpression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return ( new MysqlFilterBuilder( $this->query_parameters ) )
            ->build_sql_from( $filter_expression );
    }
}