<?php

namespace Haijin\Persistency\Query_Builder;

use Haijin\Tools\OrderedCollection;

class Filter_Expression extends Expression
{
    protected $filter;

    /// Initializing

    public function __construct($expression_context, $filter = null)
    {
        parent::__construct( $expression_context );

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
