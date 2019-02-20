<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Expressions\Filter_Visitor;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_In_Filter_Builder;

class Sql_Filter_Builder extends Filter_Visitor
{
    use Sql_Builder_Trait;

    /// Visiting

    /**
     * Accepts a Order_By_Expression.
     */
    public function accept_filter_expression($filter_expression)
    {
        return "where " . $this->expression_sql_from( $filter_expression->get_matching_expression() );
    }


    /// Creating instances

    protected function new_sql_expression_builder()
    {
        return Create::object( Sql_Expression_In_Filter_Builder::class );
    }
}