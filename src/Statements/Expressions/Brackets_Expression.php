<?php

namespace Haijin\Persistency\Statements\Expressions;

/**
 * Wraprs a query Expression with brackets.
 */
class Brackets_Expression extends Expression
{
    use Expression_Trait;

    protected $expression;

    /// Initializing

    public function __construct($expression_context, $expression = null)
    {
        parent::__construct( $expression_context );

        $this->expression = $expression;
    }

    /// Accessing

    public function get_expression()
    {
        return $this->expression;
    }

    /// Asking

    public function is_brackets_expression()
    {
        return true;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_brackets_expression( $this );
    }
}