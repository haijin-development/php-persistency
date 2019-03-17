<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;

class Sql_Having_Builder extends Sql_Expression_Builder
{
    /// Visiting

    /**
     * Accepts a Order_By_Expression.
     */
    public function accept_having_expression($having_expression)
    {
        return $this->expression_sql_from( $having_expression->get_matching_expression() );
    }


    /// Creating instances

    protected function new_sql_expression_builder()
    {
        return Create::object(
            Sql_Expression_In_Filter_Builder::class,
            $this->collected_parameters
        );
    }
}