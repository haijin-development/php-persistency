<?php

namespace Haijin\Persistency\Statements\Expressions;

/**
 * A named parameter expression.
 */
class Named_Parameter_Expression extends Expression
{
    use Expression_Trait;

    protected $parameter_name;

    /// Initializing

    public function __construct($expression_context, $parameter_name)
    {
        parent::__construct( $expression_context );

        $this->parameter_name = $parameter_name;
    }

    /// Accessing

    public function get_parameter_name()
    {
        return $this->parameter_name;
    }

    /// Asking

    public function is_named_parameter_expression()
    {
        return true;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_named_parameter_expression( $this );
    }
}
