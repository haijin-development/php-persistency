<?php

namespace Haijin\Persistency\Sql\Expression_Builders\Common_Expressions;

/**
 * A builder of general expressions used in proyection statements.
 */
class Sql_Expression_In_Proyection_Builder extends Sql_Common_Expression_Builder_Base
{
    /**
     * Accepts a Alias_Expression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $sql = $this->visit( $alias_expression->get_aliased_expression() );

        $sql .= " as ";

        $sql .= $this->escape_sql( $alias_expression->get_alias() );

        return $sql;
    }
}