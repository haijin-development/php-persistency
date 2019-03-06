<?php

namespace Haijin\Persistency\Statements;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Update_Statement extends Statement
{
    protected $records_values_expression;
    protected $filter_expression;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->records_values_expression = null;
        $this->filter_expression = null;
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

    public function get_filter_expression()
    {
        return $this->filter_expression;
    }

    public function set_filter_expression($filter_expression)
    {
        $this->filter_expression = $filter_expression;
    }

    /// Asking

    public function is_update_statement()
    {
        return true;
    }

    public function has_records_values_expression()
    {
        return $this->records_values_expression !== null;
    }

    public function has_filter_expression()
    {
        return $this->filter_expression !== null;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_update_statement( $this );
    }

    public function execute_in($database, $named_parameters)
    {
        return $database->execute_update_statement( $this, $named_parameters );
    }
}