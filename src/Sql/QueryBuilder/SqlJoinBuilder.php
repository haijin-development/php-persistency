<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\Expressions\JoinVisitor;

class SqlJoinBuilder extends JoinVisitor
{
    use SqlBuilderTrait;

    /// Visiting

    /**
     * Accepts a JoinExpression.
     */
    public function accept_join_expression($join_expression)
    {
        return "join " . 
            $this->collection_sql_from( $join_expression ) .
            " on " .
            $this->from_field_sql_from( $join_expression ) .
            " = " .
            $this->to_field_sql_from( $join_expression );
    }

    protected function collection_sql_from($join_expression)
    {
        return $this->escape_sql(
            $join_expression->get_to_collection()->get_collection_name()
        );
    }

    protected function from_field_sql_from($join_expression)
    {
        return $this->new_sql_expression_builder()
            ->visit( $join_expression->get_from_field() );
    }

    protected function to_field_sql_from($join_expression)
    {
        return $this->new_sql_expression_builder()
            ->visit( $join_expression->get_to_field() );
    }

    protected function new_sql_expression_builder()
    {
        return new SqlExpressionBuilder();
    }
}