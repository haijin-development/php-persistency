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

    public function is_proyection_expression()
    {
        return true;
    }

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

    /**
     * Sets the collection of proyected expressions.
     */
    public function set_proyected_expressions($proyected_expressions)
    {
        $this->proyected_expressions = $proyected_expressions;
    }

    /// Adding proyected expressions

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

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_proyection_expression( $this );
    }
}
