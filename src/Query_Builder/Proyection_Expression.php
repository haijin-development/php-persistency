<?php

namespace Haijin\Persistency\Query_Builder;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Proyection_Expression extends Expression
{
    protected $proyected_expressions;

    /// Initializing

    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->proyected_expressions = Create::an( Ordered_Collection::class )->with();
    }

    /// Asking

    /**
     * Returns true if the collection of proyected expressions is empty, false otherwise.
     */
    public function is_empty()
    {
        return $this->proyected_expressions->is_empty();
    }

    /**
     * Returns true if the collection of proyected expressions is not empty, false otherwise.
     */
    public function not_empty()
    {
        return $this->proyected_expressions->not_empty();
    }

    /// Accessing

    /**
     * Returns the collection of proyected expressions.
     */
    public function get_proyected_expressions()
    {
        return $this->proyected_expressions;
    }

    /// Adding proyected expressions

    /**
     * Adds a proyected expression to the collection of proyected_expressions.
     *
     * @param Expression $proyected_expression The expression to add to the query proyections.
     */
    public function add($proyected_expression)
    {
        $this->add_all( [ $proyected_expression ] );

        return $this;
    }

    /**
     * Adds all the $proyected_expressions to the collection of proyected_expressions.
     *
     * @param Expression $proyected_expression The expression to add to the query proyections.
     */
    public function add_all($proyected_expressions)
    {
        $this->proyected_expressions->add_all( $proyected_expressions );

        return $this;
    }

    /// Querying

    /**
     * Returns the number of proyected_expressions.
     *
     * @return int The number of proyected expression.
     */
    public function proyections_count()
    {
        $this->proyected_expressions->size();
    }

    /**
     * Returns the proyected_expressions at an index.
     *
     * @param int $index The index of the proyected expressions to return.
     *
     * @return Expression The proyected expression at the $index.
     */
    public function at($index)
    {
        return $this->proyected_expressions[ $index ];
    }

    /**
     * Puts the proyected_expressions at an index.
     *
     * @param int $index The index of the proyected expressions to return.
     * @param Expression $proyected_expression The proyected Expression to put at the $index.
     *
     * @return Expression The proyected expression at the $index.
     */
    public function at_put($index, $proyected_expression)
    {
        $this->proyected_expressions[ $index ] = $proyected_expression;
    }

    /// Iterating

    public function proyected_expressions_do($closure, $binding = null)
    {
        return $this->proyected_expressions->each_do( $closure, $binding );
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_proyection_expression( $this );
    }
}
