<?php

namespace Haijin\Persistency\Query_Builder\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Query_Builder\Expression;
use Haijin\Ordered_Collection;

class Field_Value_Expression extends Expression
{
    protected $field_name;
    protected $value;

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

    public function get_value()
    {
        return $this->value;
    }

    /// DSL

    public function value($expression)
    {
        $this->value = $expression;

        return $this;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_field_value_expression( $this );
    }
}
