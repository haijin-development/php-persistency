<?php

namespace Haijin\Persistency\Statements;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Query_Statement extends Statement
{
    protected $proyection_expression;
    protected $filter_expression;
    protected $join_expressions;
    protected $order_by_expression;
    protected $pagination_expression;
    protected $eager_fetch_spec;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->proyection_expression = $this->new_proyection_expression();
        $this->filter_expression = null;
        $this->join_expressions = Create::an( Ordered_Collection::class )->with();
        $this->order_by_expression = null;
        $this->pagination_expression = null;
        $this->eager_fetch_spec = null;
    }

    /// Accessing

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

    public function get_eager_fetch_spec()
    {
        return $this->eager_fetch_spec;
    }

    public function set_eager_fetch_spec($eager_fetch_spec)
    {
        $this->eager_fetch_spec = $eager_fetch_spec;
    }

    /// Asking

    public function is_query_statement()
    {
        return true;
    }

    public function has_collection_expression()
    {
        return $this->collection_expression !== null;
    }

    public function has_proyection_expression()
    {
        return $this->proyection_expression->not_empty();
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

    public function has_eager_fetch_spec()
    {
        return $this->eager_fetch_spec !== null;
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