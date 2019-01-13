<?php

namespace Haijin\Persistency\Query_Builder\Visitors\Expressions;

use Haijin\Persistency\Query_Builder\Visitors\Abstract_Query_Expression_Visitor;
use Haijin\Persistency\Query_Builder\Visitors\Query_Visitor_Trait;

class Order_By_Visitor extends Abstract_Query_Expression_Visitor
{
    use Query_Visitor_Trait;

    /**
     * Accepts a Order_By_Expression.
     */
    public function accept_order_by_expression($order_by_expression)
    {
        $this->raise_unexpected_expression_error( $order_by_expression );
    }
}