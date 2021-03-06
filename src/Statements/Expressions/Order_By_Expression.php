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

    /**
     * Returns true if the collection of proyected expressions is empty, false otherwise.
     */
    public function is_empty()
    {
        return $this->order_by_expressions->is_empty();
    }

    /**
     * Returns true if the collection of proyected expressions is not empty, false otherwise.
     */
    public function not_empty()
    {
        return $this->order_by_expressions->not_empty();
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
     * Adds an order_by expression.
     */
    public function add($order_by_expression)
    {
        $this->add_all( [ $order_by_expression ] );

        return $this;
    }

    /**
     * Adds all the $expressions.
     */
    public function add_all($expressions)
    {
        $this->order_by_expressions->add_all( $expressions );

        return $this;
    }

    /// Querying

    /// Iterating

    public function order_by_expressions_do($closure, $binding = null)
    {
        return $this->order_by_expressions->each_do( $closure, $binding );
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_order_by_expression( $this );
    }
}
