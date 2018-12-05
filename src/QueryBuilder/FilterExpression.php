<?php

namespace Haijin\Persistency\QueryBuilder;

use Haijin\Tools\OrderedCollection;

class FilterExpression extends Expression
{
    protected $filter;

    /// Initializing

    public function __construct($macro_expressions, $filter = null)
    {
        parent::__construct( $macro_expressions );

        $this->filter = $filter;
    }

    /// Accessing

    /**
     * Returns the filter.
     */
    public function get_filter()
    {
        return $this->filter;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_filter_expression( $this );
    }
}
