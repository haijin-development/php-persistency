<?php

namespace Haijin\Persistency\Statements;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Create_Statement extends Statement
{
    protected $records_values_expression;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->records_values_expression = null;
    }

    /// Accessing

    public function get_records_values_expression()
    {
        return $this->records_values_expression;
    }

    public function set_records_values_expression($records_values_expression)
    {
        $this->records_values_expression = $records_values_expression;
    }

    /// Asking

    public function is_create_statement()
    {
        return true;
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