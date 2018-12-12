<?php

namespace Haijin\Persistency\QueryBuilder\Expressions;

use Haijin\Persistency\QueryBuilder\Expression;

/**
 * A constant value expression.
 */
class ValueExpression extends Expression
{
    use ExpressionTrait;

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
        return $visitor->accept_value( $this );
    }
}
