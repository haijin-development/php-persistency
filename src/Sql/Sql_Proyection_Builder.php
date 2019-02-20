<?php

namespace Haijin\Persistency\Sql;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Expressions\Proyection_Visitor;
use Haijin\Persistency\Sql\Expression_Builders\Sql_Expression_In_Proyection_Builder;
use Haijin\Ordered_Collection;

class Sql_Proyection_Builder extends Proyection_Visitor
{
    use Sql_Builder_Trait;

    public function proyections_from($proyection_expression)
    {
        if( $proyection_expression->is_empty() ) {
            return $this->empty_proyection_sql_from( $proyection_expression );
        }

        return $this->expressions_list(
            $proyection_expression->get_proyected_expressions()
        );
    }

    protected function empty_proyection_sql_from($proyection_expression)
    {
        return $proyection_expression->get_context_collection()
                    ->get_referenced_name() . ".*";
    }

    /// Creating instances

    protected function new_sql_expression_builder()
    {
        return Create::object( Sql_Expression_In_Proyection_Builder::class );
    }
}