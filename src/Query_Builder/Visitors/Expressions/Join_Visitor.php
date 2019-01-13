<?php

namespace Haijin\Persistency\Query_Builder\Visitors\Expressions;

use Haijin\Persistency\Query_Builder\Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Query_Builder\Visitors\Query_Visitor_Trait;

class Join_Visitor extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;

    /**
     * Accepts a Join_Expression.
     */
    public function accept_join_expression($join_expression)
    {
        $this->raise_unexpected_expression_error( $join_expression );
    }
}