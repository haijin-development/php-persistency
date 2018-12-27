<?php

namespace Haijin\Persistency\Sql\QueryBuilder\ExpressionBuilders;

/**
 * A builder of general expressions used in proyection statements.
 */
class SqlExpressionInProyectionBuilder extends SqlExpressionBuilderBase
{
    /**
     * Accepts a AliasExpression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $sql = $this->visit( $alias_expression->get_aliased_expression() );

        $sql .= " as ";

        $sql .= $this->escape_sql( $alias_expression->get_alias() );

        return $sql;
    }
}