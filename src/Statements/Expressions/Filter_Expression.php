<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Ordered_Collection;

class Filter_Expression extends Expression
{
    protected $matching_expression;

    /// Initializing

    public function __construct($expression_context, $matching_expression = null)
    {
        parent::__construct( $expression_context );

        $this->matching_expression = $matching_expression;
    }

    /// Accessing

    /**
     * Returns the matching matching_expression.
     */
    public function get_matching_expression()
    {
        return $this->matching_expression;
    }

    /// Asking

    public function is_filter_expression()
    {
        return true;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_filter_expression( $this );
    }
}
