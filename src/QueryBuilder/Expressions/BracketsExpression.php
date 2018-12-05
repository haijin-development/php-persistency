<?php

namespace Haijin\Persistency\QueryBuilder\Expressions;

use Haijin\Persistency\QueryBuilder\Expression;

/**
 * Wraprs a query Expression with brackets.
 */
class BracketsExpression extends Expression
{
    use ExpressionTrait;

    protected $expression;

    /// Initializing

    public function __construct($macro_expressions, $expression = null)
    {
        parent::__construct( $macro_expressions );

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