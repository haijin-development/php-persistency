<?php

namespace Haijin\Persistency\Query_Builder\Expressions;

use Haijin\Persistency\Query_Builder\Expression;

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

    public function set_expression($expression)
    {
        $this->expression = $expression;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_brackets_expression( $this );
    }
}