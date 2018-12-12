<?php

namespace Haijin\Persistency\QueryBuilder\Expressions;

use Haijin\Persistency\QueryBuilder\Expression;

/**
 * An expression that references a field.
 */
class FieldExpression extends Expression
{
    use ExpressionTrait;

    protected $field_name;

    /// Initializing

    public function __construct($expression_context, $field_name)
    {
        parent::__construct( $expression_context );

        $this->field_name = $field_name;
    }

    /// Accessing

    public function get_field_name()
    {
        return $this->field_name;
    }

    /// Asking

    public function is_relative()
    {
        return ! $this->is_absolute();
    }

    public function is_absolute()
    {
        return strstr( $this->field_name, "." );
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_field_expression( $this );
    }
}
