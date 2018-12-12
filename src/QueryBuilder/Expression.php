<?php

namespace Haijin\Persistency\QueryBuilder;

/**
 * Base class for query expressions.
 */
abstract class Expression
{
    use ExpressionsFactoryTrait;

    protected $context;

    /// Initializing

    public function __construct($expression_context)
    {
        $this->context = $expression_context;
    }

    /// Macro expressions

    public function get_context()
    {
        return $this->context;
    }

    public function set_context($expression_context)
    {
        $this->context = $expression_context;
    }

    public function get_macros_dictionary()
    {
        return $this->get_context()->get_macros_dictionary();
    }

    public function get_context_collection()
    {
        return $this->get_context()->get_current_collection();
    }

    /// Visiting

    abstract public function accept_visitor($visitor);
}