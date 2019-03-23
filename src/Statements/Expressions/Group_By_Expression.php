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

    public function is_group_by_expression()
    {
        return true;
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
     * Adds all the $groupping_expressions to the collection of groupping_expressions.
     *
     * @param Expression $groupping_expressions The expressions to add.
     */
    public function add_all($groupping_expressions)
    {
        $this->groupping_expressions->add_all( $groupping_expressions );

        return $this;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_group_by_expression( $this );
    }
}
