<?php

namespace Haijin\Persistency\Sql\QueryBuilder\ExpressionBuilders;

/**
 * A builder of general expressions used in order by statements.
 */
class SqlExpressionInOrderByBuilder extends SqlExpressionBuilderBase
{
    public function accept_field_expression($field_expression)
    {
        return $this->escape_sql( $field_expression->get_field_name() );
    }
}