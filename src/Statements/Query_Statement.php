<?php

namespace Haijin\Persistency\Statements;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;
use Haijin\Persistency\Statements\Expressions\Expression;

class Query_Statement extends Expression
{
    protected $collection_expression;
    protected $proyection_expression;
    protected $filter_expression;
    protected $join_expressions;
    protected $order_by_expression;
    protected $pagination_expression;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->collection_expression = null;
        $this->proyection_expression = $this->new_proyection_expression();
        $this->filter_expression = null;
        $this->join_expressions = Create::an( Ordered_Collection::class )->with();
        $this->order_by_expression = null;
        $this->pagination_expression = null;
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

    /**
     * Returns the proyection expression.
     */
    public function get_proyection_expression()
    {
        return $this->proyection_expression;
    }

    /**
     * Sets the proyection_expression.
     */
    public function set_proyection_expression($proyection_expression)
    {
        $this->proyection_expression = $proyection_expression;
    }

    public function add_join_expression($join_expression)
    {
        $this->join_expressions->add( $join_expression );

        return $join_expression;
    }

    public function get_join_expressions()
    {
        return $this->join_expressions;
    }

    /**
     * Returns the filter expression.
     */
    public function get_filter_expression()
    {
        return $this->filter_expression;
    }

    /**
     * Sets the filter_expression.
     */
    public function set_filter_expression($filter_expression)
    {
        $this->filter_expression = $filter_expression;
    }

    /**
     * Returns the order_by expressions.
     */
    public function get_order_by_expression()
    {
        return $this->order_by_expression;
    }

    /**
     * Sets the order_by_expressions.
     */
    public function set_order_by_expression($order_by_expressions)
    {
        $this->order_by_expression = $order_by_expressions;
    }

    /**
     * Returns the pagination expression.
     */
    public function get_pagination_expression()
    {
        return $this->pagination_expression;
    }

    /**
     * Sets the proyection_expression.
     */
    public function set_pagination_expression($pagination_expression)
    {
        $this->pagination_expression = $pagination_expression;
    }

    /// Asking

    public function has_collection_expression()
    {
        return $this->collection_expression !== null;
    }

    public function has_proyections()
    {
        return $this->proyection->not_empty();
    }

    public function has_filter_expression()
    {
        return $this->filter_expression !== null;
    }

    public function has_join_expressions()
    {
        return $this->join_expressions->not_empty();
    }

    public function has_order_by_expression()
    {
        return $this->order_by_expression !== null;
    }

    public function has_pagination_expression()
    {
        return $this->pagination_expression !== null;
    }

    /// Iterating

    public function join_expressions_do($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        return $this->join_expressions->each_do( $closure, $binding );
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_query_statement( $this );
    }

    public function execute_in($database, $named_parameters)
    {
        return $database->execute_query_statement( $this, $named_parameters );
    }
}