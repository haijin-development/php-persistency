<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Order_By_Expression extends Expression
{
    protected $order_by_expressions;

    /// Initializing

    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->order_by_expressions = new Ordered_Collection();
    }

    /// Asking

    public function is_order_by_expression()
    {
        return true;
    }

    /// Accessing

    /**
     * Returns the collection of order_by expressions.
     */
    public function get_order_by_expressions()
    {
        return $this->order_by_expressions;
    }

    /// Adding proyected expressions

    /**
     * Adds all the $expressions.
     */
    public function add_all($expressions)
    {
        $this->order_by_expressions->add_all( $expressions );

        return $this;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_order_by_expression( $this );
    }
}
