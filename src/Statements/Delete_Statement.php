<?php

namespace Haijin\Persistency\Statements;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Delete_Statement extends Statement
{
    protected $filter_expression;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->filter_expression = null;
    }

    /// Accessing

    public function get_filter_expression()
    {
        return $this->filter_expression;
    }

    public function set_filter_expression($filter_expression)
    {
        $this->filter_expression = $filter_expression;
    }

    /// Asking

    public function has_filter_expression()
    {
        return $this->filter_expression !== null;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_delete_statement( $this );
    }

    public function execute_in($database, $named_parameters)
    {
        return $database->execute_delete_statement( $this, $named_parameters );
    }
}