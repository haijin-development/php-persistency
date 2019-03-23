<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Record_Values_Expression extends Expression
{
    protected $field_values;

    /// Initializing

    public function __construct($expression_context, $field_values = [])
    {
        parent::__construct( $expression_context );

        $this->field_values = $field_values;
    }

    /// Asking

    public function is_record_values_expression()
    {
        return true;
    }

    /// Accessing

    /**
     * Returns the records field_values.
     */
    public function get_field_values()
    {
        return $this->field_values;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_record_values_expression( $this );
    }
}
