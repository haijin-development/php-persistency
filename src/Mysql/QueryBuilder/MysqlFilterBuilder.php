<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Sql\QueryBuilder\SqlBuilderTrait;
use Haijin\Persistency\Sql\QueryBuilder\SqlFilterBuilder;

/**
 * A FilterVisitor subclass to handle FilterExpressions according to Mysql queries requirements.
 * See Haijin\Persistency\Sql\QueryBuilder\SqlExpressionBuilder\SqlFilterBuilder class
 * for the complete protocol of this class.
 */
class MysqlFilterBuilder extends SqlFilterBuilder
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
     * Overrides the super class method to return a MysqlExpressionBuilder instead.
     *
     * @return MysqlExpressionBuilder A new MysqlExpressionBuilder.
     */
    protected function new_sql_expression_builder()
    {
        return new MysqlExpressionBuilder( $this->query_parameters );
    }
}