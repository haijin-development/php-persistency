<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

class MysqlQueryBuilder extends SqlBuilder
{
    /**
     * Accepts a PaginationExpression.
     */
    public function accept_pagination_expression($pagination_expression)
    {
        return ( new MysqlPaginationBuilder() )
            ->build_sql_from( $pagination_expression );
    }
}