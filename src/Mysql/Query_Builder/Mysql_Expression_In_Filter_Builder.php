<?php

namespace Haijin\Persistency\Mysql\Query_Builder;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Sql\Query_Builder\Expression_Builders\Sql_Expression_In_Filter_Builder;

/**
 * A Sql_Expression_In_Filter_Builder subclass to handle ValueExpressions and NamedParameterExpressions
 * according to Mysql queries requirements.
 * See Haijin\Persistency\Sql\Query_Builder\Expression_Builders\Sql_Expression_In_Filter_Builder class
 * for the complete protocol of this class.
 */
class Mysql_Expression_In_Filter_Builder extends Sql_Expression_In_Filter_Builder
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
     * @param Value_Expression $value_expression The Value_Expression to accept.
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
     * Adds a Named_Parameter_Expression placeholder to the OrderedCollection of the query
     * parameters and returns the sql to append to the query sql.
     *
     * @param Named_Parameter_Expression $named_parameter_expression The Named_Parameter_Expression
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
     * Returns a new Named_Parameter_Placerholder on the $parameter_name.
     *
     * @param string $parameter_name The name of the parameter.
     *
     * @return Named_Parameter_Placerholder The new Named_Parameter_Placerholder.
     */
    protected function new_named_parameter_placeholder($parameter_name)
    {
        return new Named_Parameter_Placerholder( $parameter_name );
    }

    protected function new_sql_expression_builder()
    {
        return Create::a( get_class( $this ) )->with( $this->query_parameters );
    }
}