<?php

namespace Haijin\Persistency\Statements_Visitors\Expressions;

use Haijin\Persistency\Statements_Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Statements_Visitors\Query_Visitor_Trait;

class Collection_Visitor extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;

    /**
     * Accepts a Collection_Expression.
     */
    public function accept_collection_expression($collection_expression)
    {
        $this->raise_unexpected_expression_error( $collection_expression );
    }
}