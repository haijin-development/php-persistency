<?php

namespace Haijin\Persistency\Query_Builder\Expressions;

use Haijin\Persistency\Query_Builder\Expression;
use Haijin\Persistency\Query_Builder\Expressions_DSL_Trait;
use Haijin\Persistency\Query_Builder\Builders\Expression_Builder;

/**
 * A expression with a binary operator like ( 1 + 3 ).
 */
class Binary_Operator_Expression extends Expression
{
    use Expression_Trait;

    protected $operator_symbol;
    protected $parameter_1;
    protected $parameter_2;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context, $operator_symbol, $parameter_1, $parameter_2)
    {
        parent::__construct( $expression_context );

        $this->operator_symbol = $operator_symbol;
        $this->parameter_1 = $parameter_1;
        $this->parameter_2 = $parameter_2;
    }

    /// Accessing

    public function get_operator_symbol()
    {
        return $this->operator_symbol;
    }

    public function get_parameter_1()
    {
        return $this->parameter_1;
    }

    public function set_parameter_1($expression)
    {
        $this->parameter_1 = $expression;
    }

    public function get_parameter_2()
    {
        return $this->parameter_2;
    }

    public function set_parameter_2($expression)
    {
        $this->parameter_2 = $expression;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_binary_operator_expression( $this );
    }

    /// DSL

    public function brackets($expression)
    {
        $this->set_parameter_2( $this->new_brackets_expression($expression) );

        return $this;
    }

    /**
     * Implementation artifact. Assumes that an unkown function call is the parameter_2
     * expression.
     * Returns a $this Binary_Operator_Expression.
     */
    public function __call($function_name, $parameters)
    {
        $expression_builder = new Expression_Builder( $this->context );

        $this->set_parameter_2(
            $expression_builder->receive($function_name, $parameters)
        );

        return $this;
    }

    /**
     * Assumes that the attribute is a macro expressions. Searches for a defined macro
     * expression with that name and returns its evaluation. If none is found raises
     * an error.
     */
    public function __get($macro_name)
    {
        $this->set_parameter_2(
            $this->get_macros_dictionary()->at( $macro_name, $this )
        );

        return $this;
    }
}