<?php

namespace Haijin\Persistency\Engines\Elasticsearch;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Statements_Visitors\Query_Visitor_Trait;
use Haijin\Persistency\Engines\Named_Parameter_Placerholder;

class Elasticsearch_Filter_Builder extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;

    /**
     * Accepts a Filter_Expression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return $this->visit( $filter_expression->get_matching_expression() );
    }

    /**
     * Accepts a Function_Call_Expression.
     */
    public function accept_function_call_expression($function_call_expression)
    {
        $filter = new \stdclass();

        $function_name = $function_call_expression->get_function_name();

        $parameters = $function_call_expression->get_parameters();

        if( count( $parameters ) == 2
            &&
            ( $parameters[ 0 ]->is_field_expression() || $parameters[ 0 ]->is_value_expression() )
          )
        {
            $filter->$function_name = new \stdclass();

            $key = $this->visit( $parameters[ 0 ] );
            $value = $this->visit( $parameters[ 1 ] );

            $filter->$function_name->$key = $value;

            return $filter;

        }

        $filter = new \stdclass();

        $filter->$function_name = [];

        $multiple_terms = false;

        foreach( $parameters as $function_expression ) {

            $filter->$function_name[] = $this->visit( $function_expression );
        }

        $params_count = count( $filter->$function_name );

        if( $params_count == 0 ) {
            $filter->$function_name = new \stdclass();
        }

        if( $params_count == 1 ) {
            $filter->$function_name = $filter->$function_name[ 0 ];
        }

        return $filter;
    }

    /**
     * Accepts a Binary_Operator_Expression.
     */
    public function accept_binary_operator_expression($binary_operator_expression)
    {
        return[
            $binary_operator_expression->get_operator_symbol() => [
               $this->visit( $binary_operator_expression->get_parameter_1() ),
               $this->visit( $binary_operator_expression->get_parameter_2() )
            ]
        ];
    }
    /**
     * Accepts a Field_Expression.
     */
    public function accept_field_expression($field_expression)
    {
        return $field_expression->get_field_name();
    }

    /**
     * Accepts a Value_Expression.
     */
    public function accept_value_expression($value_expression)
    {
        return $value_expression->get_value();
    }

    /**
     * Accepts a Named_Parameter_Expression.
     */
    public function accept_named_parameter_expression($named_parameter_expression)
    {
        return $this->new_named_parameter_placeholder(
                $named_parameter_expression->get_parameter_name()
            );
    }

    protected function new_named_parameter_placeholder($parameter_name)
    {
        return Create::a( Named_Parameter_Placerholder::class )->with( $parameter_name );
    }
}