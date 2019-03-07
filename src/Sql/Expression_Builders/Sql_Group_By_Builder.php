<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Group_By_Builder;

class Sql_Group_By_Builder extends Sql_Expression_Builder
{
    /// Visiting

    /**
     * Accepts a Order_By_Expression.
     */
    public function accept_group_by_expression($group_by_expression)
    {
        return $this->expressions_list(
                $group_by_expression->get_groupping_expressions()->to_array()
            );
    }

    protected function new_sql_expression_builder()
    {
        return Create::object(
            Sql_Expression_In_Group_By_Builder::class,
            $this->collected_parameters
        );
    }
}