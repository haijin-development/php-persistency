<?php

namespace Haijin\Persistency\Query_Builder\Visitors\Expressions;

use Haijin\Persistency\Query_Builder\Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Query_Builder\Visitors\Query_Visitor_Trait;

class Filter_Visitor extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;

    /**
     * Accepts a Filter_Expression.
     */
    public function accept_filter_expression($filter_expression)
    {
        $this->raise_unexpected_expression_error( $filter_expression );
    }
}