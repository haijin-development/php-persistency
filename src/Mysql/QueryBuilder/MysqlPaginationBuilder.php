<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilderTrait;
use Haijin\Persistency\Sql\QueryBuilder\SqlPaginationBuilder;

/**
 * A SqlPaginationBuilder subclass to handle PaginationExpressions according to Mysql queries
 * requirements.
 * See Haijin\Persistency\Sql\QueryBuilder\SqlPaginationBuilder class
 * for the complete protocol of this class.
 */
class MysqlPaginationBuilder extends SqlPaginationBuilder
{
    /// Building sql

    /**
     * Returns the "limit" part of the sql query.
     * Raises an error if the mandatory $limit is null.
     *
     * @param int $offset The offset of the query. May be null.
     * @param int $offset The limit of the query. Must be >= 0.
     *
     * @return string The "limit" part of the sql query.
     */
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