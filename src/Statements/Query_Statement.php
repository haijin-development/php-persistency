<?php

namespace Haijin\Persistency\Statements;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Query_Statement extends Statement
{
    protected $proyection_expression;
    protected $join_expressions;
    protected $filter_expression;
    protected $group_by_expression;
    protected $having_expression;
    protected $order_by_expression;
    protected $pagination_expression;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->proyection_expression = $this->new_proyection_expression();
        $this->join_expressions = new Ordered_Collection();
        $this->filter_expression = null;
        $this->group_by_expression = null;
        $this->having_expression = null;
        $this->order_by_expression = null;
        $this->pagination_expression = null;
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
     * Returns the having expression.
     */
    public function get_having_expression()
    {
        return $this->having_expression;
    }

    /**
     * Sets the having_expression.
     */
    public function set_having_expression($having_expression)
    {
        $this->having_expression = $having_expression;
    }

    /**
     * Gets the group_by_expression.
     */
    public function get_group_by_expression()
    {
        return $this->group_by_expression;
    }

    /**
     * Sets the group_by_expression.
     */
    public function set_group_by_expression($group_by_expression)
    {
        $this->group_by_expression = $group_by_expression;
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

    public function is_query_statement()
    {
        return true;
    }

    public function has_proyection_expression()
    {
        return $this->proyection_expression->not_empty();
    }

    public function has_join_expressions()
    {
        return $this->join_expressions->not_empty();
    }

    public function has_filter_expression()
    {
        return $this->filter_expression !== null;
    }

    public function has_group_by_expression()
    {
        return $this->group_by_expression !== null;
    }

    public function has_having_expression()
    {
        return $this->having_expression !== null;
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

    public function join_expressions_do($callable)
    {
        return $this->join_expressions->each_do( $callable );
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