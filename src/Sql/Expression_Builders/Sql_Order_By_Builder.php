<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Order_By_Builder;

class Sql_Order_By_Builder extends Sql_Expression_Builder
{
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
        return Create::object(
            Sql_Expression_In_Order_By_Builder::class,
            $this->collected_parameters
        );
    }
}