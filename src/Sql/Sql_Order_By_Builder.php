<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Expressions\Order_By_Visitor;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_In_Order_By_Builder;

class Sql_Order_By_Builder extends Order_By_Visitor
{
    use Sql_Builder_Trait;

    /// Visiting

    /**
     * Accepts a Order_By_Expression.
     */
    public function accept_order_by_expression($order_by_expression)
    {
        return $this->expressions_list(
                $order_by_expression->get_order_by_expressions()
            );
    }

    protected function new_sql_expression_builder()
    {
        return Create::object( Sql_Expression_In_Order_By_Builder::class );
    }
}