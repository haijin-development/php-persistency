<?php

namespace Haijin\Persistency\QueryBuilder;

/**
 * Base class for query expressions.
 */
abstract class Expression
{
    use ExpressionsFactoryTrait;

    protected $macro_expressions;

    /// Initializing

    public function __construct($macro_expressions)
    {
        $this->macro_expressions = $macro_expressions;
    }

    /// Macro expressions

    public function get_macro_expressions()
    {
        return $this->macro_expressions;
    }

    /**
     * Assumes that the attribute is a macro expressions. Searches for a defined macro
     * expression with that name and returns its evaluation. If none is found raises
     * an error.
     */
    public function __get($attribute_name)
    {
        return $this->macro_expressions->at( $attribute_name, $this );
    }

    /// Visiting

    abstract public function accept_visitor($visitor);
}