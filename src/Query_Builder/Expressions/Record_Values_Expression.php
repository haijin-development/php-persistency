<?php

namespace Haijin\Persistency\Query_Builder\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Query_Builder\Expression;
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

    /**
     * Returns true if the collection of proyected expressions is empty, false otherwise.
     */
    public function is_empty()
    {
        return $this->field_values == [];
    }

    /**
     * Returns true if the collection of proyected expressions is not empty, false otherwise.
     */
    public function not_empty()
    {
        return ! $this->is_empty();
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
