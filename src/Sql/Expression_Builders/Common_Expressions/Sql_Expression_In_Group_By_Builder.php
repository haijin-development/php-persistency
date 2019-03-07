<?php

namespace Haijin\Persistency\Sql\Expression_Builders\Common_Expressions;

/**
 * A builder of general expressions used in group by statements.
 */
class Sql_Expression_In_Group_By_Builder extends Sql_Common_Expression_Builder_Base
{
    public function accept_field_expression($field_expression)
    {
        return $this->escape_sql( $field_expression->get_field_name() );
    }
}