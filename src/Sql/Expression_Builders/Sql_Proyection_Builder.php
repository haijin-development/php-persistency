<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Proyection_Builder;

class Sql_Proyection_Builder extends Sql_Expression_Builder
{
    public function proyections_from($proyection_expression)
    {
        if( $proyection_expression->is_empty() ) {
            return $this->empty_proyection_sql_from( $proyection_expression );
        }

        return $this->expressions_list(
            $proyection_expression->get_proyected_expressions()->to_array()
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
        return Create::object(
            Sql_Expression_In_Proyection_Builder::class,
            $this->collected_parameters
        );
    }
}