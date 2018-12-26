<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilder;

/**
 * A SqlBuilder subclass to handle QueryExpressions according to Mysql queries requirements.
 * See Haijin\Persistency\Sql\QueryBuilder\SqlExpressionBuilder\SqlBuilder class
 * for the complete protocol of this class.
 */
class MysqlQueryBuilder extends SqlBuilder
{
    /**
     * An OrderedCollection with the collected query parameters from ValueExpressions and
     * from NamedParameterExpressions.
     */
    protected $query_parameters;

    /// Initializing

    /**
     * Initializes $this instance.
     *
     * @param OrderedCollection $query_parameters An OrderedCollection to collect query parameters
     * from ValueExpressions and from NamedParameterExpressions.
     */
    public function __construct($query_parameters)
    {
        $this->query_parameters = $query_parameters;
    }

    /// Creating instances

    /**
     * Returns a new MysqlPaginationBuilder.
     */
    protected function new_sql_pagination_builder()
    {
        return new MysqlPaginationBuilder( $this->query_parameters );
    }

    /**
     * Returns a new MysqlFilterBuilder.
     */
    protected function new_sql_filter_builder()
    {
        return new MysqlFilterBuilder( $this->query_parameters );
    }
}