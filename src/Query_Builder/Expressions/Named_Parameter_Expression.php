<?php

namespace Haijin\Persistency\Query_Builder\Expressions;

use Haijin\Persistency\Query_Builder\Expression;

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

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_named_parameter_expression( $this );
    }
}
