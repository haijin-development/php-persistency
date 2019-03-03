<?php

namespace Haijin\Persistency\Statements\Expressions;

/**
 * Trait with methods to create query expressions.
 */
trait Expressions_DSL_Trait
{
    /// DSL

    /**
     * Returns a new Count_Expression.
     */
    public function count()
    {
        return $this->new_count_expression();
    }

    /**
     * Returns a new All_Fields_Expression.
     */
    public function all()
    {
        return $this->new_all_fields_expression();
    }

    /**
     * Returns a new Field_Expression.
     */
    public function field($field_name)
    {
        return $this->new_field_expression( $field_name );
    }

    /**
     * Returns a new Value_Expression.
     */
    public function value($value)
    {
        return $this->new_value_expression( $value );
    }

    /**
     * Returns a new Named_Parameter_Expression.
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

    public function ignore()
    {
        return $this->new_ignore_expression();
    }

    /**
     * Assumes that an unkown function call is a query function call, therefore creates and
     * returns a Function_Call_Expression.
     */
    public function __call($function_name, $parameters)
    {
        return $this->new_function_call_expression(
            $function_name,
            $this->_values_to_expressions( $parameters )
        );
    }
}