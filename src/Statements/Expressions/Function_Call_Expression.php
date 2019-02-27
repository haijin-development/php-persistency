<?php

namespace Haijin\Persistency\Statements\Expressions;

/**
 * A function call expression like f(1, 2).
 */
class Function_Call_Expression extends Expression
{
    use Expression_Trait;

    protected $function_name;
    protected $parameters;

    /// Initializing

    public function __construct($expression_context, $function_name, $parameters)
    {
        parent::__construct( $expression_context );

        $this->function_name = $function_name;
        $this->parameters = $parameters;
    }

    /// Accessing

    public function get_function_name()
    {
        return $this->function_name;
    }

    public function get_parameters()
    {
        return $this->parameters;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_function_call_expression( $this );
    }

    /// Asking

    public function is_function_call_expression()
    {
        return true;
    }
}