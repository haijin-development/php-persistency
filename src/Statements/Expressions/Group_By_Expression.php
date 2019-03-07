<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Ordered_Collection;

class Group_By_Expression extends Expression
{
    protected $groupping_expressions;

    /// Initializing

    public function __construct($expression_context)
    {
        parent::__construct( $expression_context );

        $this->groupping_expressions = new Ordered_Collection();
    }

    /// Asking

    /**
     * Returns true if the collection of proyected expressions is empty, false otherwise.
     */
    public function is_empty()
    {
        return $this->groupping_expressions->is_empty();
    }

    /**
     * Returns true if the collection of proyected expressions is not empty, false otherwise.
     */
    public function not_empty()
    {
        return $this->groupping_expressions->not_empty();
    }

    /// Accessing

    /**
     * Returns the collection of groupping expressions.
     */
    public function get_groupping_expressions()
    {
        return $this->groupping_expressions;
    }

    /// Adding groupping expressions

    /**
     * Adds a gtopuing expression to the collection of groupping_expressions.
     *
     * @param Expression $groupping_expression The expression to add.
     */
    public function add($groupping_expressions)
    {
        $this->add_all( [ $proyected_expression ] );

        return $this;
    }

    /**
     * Adds all the $groupping_expressions to the collection of groupping_expressions.
     *
     * @param Expression $groupping_expressions The expressions to add.
     */
    public function add_all($groupping_expressions)
    {
        $this->groupping_expressions->add_all( $groupping_expressions );

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
        return $visitor->accept_group_by_expression( $this );
    }
}
