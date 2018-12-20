<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Persistency\Sql\QueryBuilder\SqlExpressionBuilder;

/**
 * A builder of general expressions used in different query parts.
 */
class MysqlExpressionBuilder extends SqlExpressionBuilder
{
    protected $query_parameters;

    public function __construct($query_parameters)
    {
        parent::__construct();

        $this->query_parameters = $query_parameters;
    }

    /**
     * Accepts a ValueExpression.
     */
    public function accept_value($value_expression)
    {
        $this->query_parameters->add( $value_expression->get_value() );

        return "?";
    }
}