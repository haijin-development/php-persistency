<?php

namespace Haijin\Persistency\QueryBuilder\Expressions;

use Haijin\Persistency\QueryBuilder\Expression;

/**
 * A named parameter expression.
 */
class NamedParameterExpression extends Expression
{
    use ExpressionTrait;

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
