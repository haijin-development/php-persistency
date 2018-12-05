<?php

namespace Haijin\Persistency\QueryBuilder\Expressions;

use Haijin\Persistency\QueryBuilder\Expression;
use Haijin\Persistency\QueryBuilder\ExpressionsDSLTrait;
use Haijin\Persistency\QueryBuilder\Builders\ExpressionBuilder;

/**
 * A expression with a binary operator like ( 1 + 3 ).
 */
class BinaryOperatorExpression extends Expression
{
    use ExpressionTrait;

    protected $operator_symbol;
    protected $parameter_1;
    protected $parameter_2;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($macro_expressions, $operator_symbol, $parameter_1, $parameter_2)
    {
        parent::__construct( $macro_expressions );

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
     * Returns a $this BinaryOperatorExpression.
     */
    public function __call($function_name, $parameters)
    {
        $expression_builder = new ExpressionBuilder( $this->macro_expressions );

        $this->set_parameter_2(
            $expression_builder->receive($function_name, $parameters)
        );

        return $this;
    }
}