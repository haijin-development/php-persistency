<?php

namespace Haijin\Persistency\QueryBuilder;

use Haijin\Tools\OrderedCollection;

/**
 * Trait with methods to create query expressions.
 */
trait ExpressionsDSLTrait
{
    /// DSL

    /**
     * Returns a new AllFieldsExpression.
     */
    public function all()
    {
        return $this->new_all_fields_expression();
    }

    /**
     * Returns a new FieldExpression.
     */
    public function field($field_name)
    {
        return $this->new_field_expression( $field_name );
    }

    /**
     * Returns a new ValueExpression.
     */
    public function value($value)
    {
        return $this->new_value_expression( $value );
    }

    /**
     * Returns a new NamedParameterExpression.
     */
    public function param($parameter_name)
    {
        return $this->new_named_parameter_expression( $parameter_name );
    }

    /**
     * Wraps a query Expression with brackets.
     */
    public function brackets($expression)
    {
        return $this->new_brackets_expression( $expression );
    }

    /**
     * Assumes that an unkown function call is a query function call, therefore creates and
     * returns a FunctionCallExpression.
     */
    public function __call($function_name, $parameters)
    {
        return $this->new_function_call_expression(
            $function_name,
            $this->_values_to_expressions( $parameters )
        );
    }

    /// Helper methods

    /**
     * Converts values to expressions.
     */
    public function _values_to_expressions($values)
    {
        return OrderedCollection::with_all( $values )
            ->collect( function($each_parameter) {
                return $this->_value_to_expression( $each_parameter );
            }, 
            $this )->to_array();
    }

    /**
     * Converts value to a ValueExpressions if it's not one.
     */
    public function _value_to_expression($value)
    {
        if( $value instanceof \Haijin\Persistency\QueryBuilder\Expression ) {
            return $value;
        } else {
            return $this->new_value_expression( $value );
        }
    }
}