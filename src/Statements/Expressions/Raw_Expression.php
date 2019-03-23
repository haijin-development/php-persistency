<?php

namespace Haijin\Persistency\Statements\Expressions;

/**
 * A value to be interpreted as is.
 */
class Raw_Expression extends Expression
{
    use Expression_Trait;

    protected $value;

    /// Initializing

    public function __construct($expression_context, $value)
    {
        parent::__construct( $expression_context );

        $this->value = $value;
    }

    /// Accessing

    public function get_value()
    {
        return $this->value;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_raw_expression( $this );
    }
}
