<?php

namespace Haijin\Persistency\Engines\Postgresql\Query_Builder;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Engines\Named_Parameter_Placerholder;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;

/**
 * A Sql_Expression_In_Filter_Builder subclass to handle ValueExpressions and 
 * NamedParameterExpressions according to Postgresql queries requirements.
 * See Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder
 * class for the complete protocol of this class.
 */
class Postgresql_Expression_In_Filter_Builder extends Sql_Expression_In_Filter_Builder
{
    /// Visiting

    /**
     * Adds a value to the Ordered_Collection of the query parameters and returns the sql to append
     * to the query sql.
     *
     * @param Value_Expression $value_expression The Value_Expression to accept.
     *
     * @return string The sql to append to the Postgresql query.
     */
    public function accept_value_expression($value_expression)
    {
        $this->collected_parameters->add(
            $value_expression->get_value()
        );

        $param_index = $this->collected_parameters->size();

        return "\${$param_index}";
    }

    /**
     * Adds a Named_Parameter_Expression placeholder to the Ordered_Collection of the query
     * parameters and returns the sql to append to the query sql.
     *
     * @param Named_Parameter_Expression $named_parameter_expression The Named_Parameter_Expression
     *      to accept.
     *
     * @return string The sql to append to the Postgresql query.
     */
    public function accept_named_parameter_expression($named_parameter_expression)
    {
        $this->collected_parameters->add(
            $this->new_named_parameter_placeholder(
                $named_parameter_expression->get_parameter_name()
            )
        );

        $param_index = $this->collected_parameters->size();

        return "\${$param_index}";
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
        return Create::object( Named_Parameter_Placerholder::class,  $parameter_name );
    }

    protected function new_sql_expression_builder()
    {
        return Create::object(
            get_class( $this ),
            $this->collected_parameters
        );
    }
}