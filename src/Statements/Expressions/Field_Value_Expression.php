<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Field_Value_Expression extends Expression
{
    protected $field_name;
    protected $value_expression;

    /// Initializing

    public function __construct($expression_context, $field_name, $value_expression)
    {
        parent::__construct( $expression_context );

        $this->field_name = $field_name;
        $this->value_expression = $value_expression;
    }

    /// Accessing

    public function get_field_name()
    {
        return $this->field_name;
    }

    public function get_value_expression()
    {
        return $this->value_expression;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_field_value_expression( $this );
    }
}
