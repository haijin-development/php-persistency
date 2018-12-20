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
    public function accept_value_expression($value_expression)
    {
        $this->query_parameters->add(
            $value_expression->get_value()
        );

        return "?";
    }

    /**
     * Accepts a NamedParameterExpression.
     */
    public function accept_named_parameter_expression($named_parameter_expression)
    {
        $this->query_parameters->add(
            $this->new_named_parameter_placeholder(
                $named_parameter_expression->get_parameter_name()
            )
        );

        return "?";
    }

    protected function new_named_parameter_placeholder($parameter_name)
    {
        return new NamedParameterPlacerholder( $parameter_name );
    }
}