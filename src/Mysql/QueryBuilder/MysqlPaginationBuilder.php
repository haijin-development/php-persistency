<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilderTrait;
use Haijin\Persistency\Sql\QueryBuilder\SqlPaginationBuilder;

class MysqlPaginationBuilder extends SqlPaginationBuilder
{
    protected function offset_and_limit_sql($offset, $limit)
    {
        if( $limit === null ) {
            return $this->raise_missing_limit_expression_error();
        }

        if( $offset === null ) {
            return "limit " . $this->escape_sql( (string) $limit );
        }

        return "limit " .
                $this->escape_sql( (string) $offset )  . ", " .
                $this->escape_sql( (string) $limit );
    }
}