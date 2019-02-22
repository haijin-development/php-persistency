<?php

namespace Haijin\Persistency\Statements;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Statements\Expressions\Expression;

class Create_Statement extends Expression
{
    protected $collection_expression;
    protected $records_values_expression;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->collection_expression = null;
        $this->records_values_expression = null;
    }

    /// Accessing

    /**
     * Returns the collection expression.
     */
    public function get_collection_expression()
    {
        return $this->collection_expression;
    }

    /**
     * Sets the collection_expression.
     */
    public function set_collection_expression($collection_expression)
    {
        $this->collection_expression = $collection_expression;
        $this->context->set_current_collection( $collection_expression );
    }

    public function get_records_values_expression()
    {
        return $this->records_values_expression;
    }

    public function set_records_values_expression($records_values_expression)
    {
        $this->records_values_expression = $records_values_expression;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_create_statement( $this );
    }

    public function execute_in($database, $named_parameters)
    {
        return $database->execute_create_statement( $this, $named_parameters );
    }
}