<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Proyection_Expression extends Expression
{
    protected $proyected_expressions;

    /// Initializing

    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->proyected_expressions = new Ordered_Collection();
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
