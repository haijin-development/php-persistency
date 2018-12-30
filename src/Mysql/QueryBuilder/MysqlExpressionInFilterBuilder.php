<?php

namespace Haijin\Persistency\Mysql\QueryBuilder;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Sql\QueryBuilder\ExpressionBuilders\SqlExpressionInFilterBuilder;

/**
 * A SqlExpressionInFilterBuilder subclass to handle ValueExpressions and NamedParameterExpressions
 * according to Mysql queries requirements.
 * See Haijin\Persistency\Sql\QueryBuilder\ExpressionBuilders\SqlExpressionInFilterBuilder class
 * for the complete protocol of this class.
 */
class MysqlExpressionInFilterBuilder extends SqlExpressionInFilterBuilder
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

    /// Visiting

    /**
     * Adds a value to the OrderedCollection of the query parameters and returns the sql to append
     * to the query sql.
     *
     * @param ValueExpression $value_expression The ValueExpression to accept.
     *
     * @return string The sql to append to the Mysql query.
     */
    public function accept_value_expression($value_expression)
    {
        $this->query_parameters->add(
            $value_expression->get_value()
        );

        return "?";
    }

    /**
     * Adds a NamedParameterExpression placeholder to the OrderedCollection of the query
     * parameters and returns the sql to append to the query sql.
     *
     * @param NamedParameterExpression $named_parameter_expression The NamedParameterExpression
     *      to accept.
     *
     * @return string The sql to append to the Mysql query.
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

    /**
     * Returns a new NamedParameterPlacerholder on the $parameter_name.
     *
     * @param string $parameter_name The name of the parameter.
     *
     * @return NamedParameterPlacerholder The new NamedParameterPlacerholder.
     */
    protected function new_named_parameter_placeholder($parameter_name)
    {
        return new NamedParameterPlacerholder( $parameter_name );
    }

    protected function new_sql_expression_builder()
    {
        return Create::a( get_class( $this ) )->with( $this->query_parameters );
    }
}