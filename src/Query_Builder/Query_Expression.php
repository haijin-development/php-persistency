<?php

namespace Haijin\Persistency\Query_Builder;

use Haijin\Ordered_Collection;

class Query_Expression extends Expression
{
    protected $collection;
    protected $proyection;
    protected $filter;
    protected $joins;
    protected $order_by;
    protected $pagination;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->collection = null;
        $this->proyection = $this->new_proyection_expression();
        $this->filter = null;
        $this->joins = new Ordered_Collection();
        $this->order_by = null;
        $this->pagination = null;
    }

    /// Accessing

    /**
     * Returns the collection expression.
     */
    public function get_collection()
    {
        return $this->collection;
    }

    /**
     * Sets the collection_expression.
     */
    public function set_collection($collection_expression)
    {
        $this->collection = $collection_expression;
        $this->context->set_current_collection( $collection_expression );
    }

    /**
     * Returns the proyection expression.
     */
    public function get_proyection()
    {
        return $this->proyection;
    }

    /**
     * Sets the proyection_expression.
     */
    public function set_proyection($proyection_expression)
    {
        $this->proyection = $proyection_expression;
    }

    public function add_join($join_expression)
    {
        $this->joins->add( $join_expression );

        return $join_expression;
    }

    public function get_joins()
    {
        return $this->joins;
    }

    /**
     * Returns the filter expression.
     */
    public function get_filter()
    {
        return $this->filter;
    }

    /**
     * Sets the filter_expression.
     */
    public function set_filter($filter_expression)
    {
        $this->filter = $filter_expression;
    }

    /**
     * Returns the order_by expressions.
     */
    public function get_order_by()
    {
        return $this->order_by;
    }

    /**
     * Sets the order_by_expressions.
     */
    public function set_order_by($order_by_expressions)
    {
        $this->order_by = $order_by_expressions;
    }

    /**
     * Returns the pagination expression.
     */
    public function get_pagination()
    {
        return $this->pagination;
    }

    /**
     * Sets the proyection_expression.
     */
    public function set_pagination($pagination_expression)
    {
        $this->pagination = $pagination_expression;
    }

    /// Asking

    public function has_collection()
    {
        return $this->collection !== null;
    }

    public function has_proyections()
    {
        return $this->proyection->not_empty();
    }

    public function has_filter()
    {
        return $this->filter !== null;
    }

    public function has_joins()
    {
        return $this->joins->not_empty();
    }

    public function has_order_by()
    {
        return $this->order_by !== null;
    }

    public function has_pagination()
    {
        return $this->pagination !== null;
    }

    /// Iterating

    public function joins_do($closure, $binding = null)
    {
        if( $binding === null ) {
            $binding = $this;
        }

        return $this->joins->each_do( $closure, $binding );
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_query_expression( $this );
    }
}