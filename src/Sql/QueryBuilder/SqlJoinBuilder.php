<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Instantiator\Create;
use Haijin\Persistency\QueryBuilder\Visitors\Expressions\JoinVisitor;
use Haijin\Persistency\Sql\QueryBuilder\ExpressionBuilders\SqlExpressionInFilterBuilder;

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
        return $this->visit( $join_expression->get_to_collection() );
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

    /**
     * Accepts a AliasExpression. The alias at this DSL level is for the CollectionExpression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $sql = $this->visit( $alias_expression->get_aliased_expression() );
        $sql .= " as ";
        $sql .= $this->escape_sql( $alias_expression->get_alias() );

        return $sql;
    }

    /**
     * Accepts a CollectionExpression.
     */
    public function accept_collection_expression($collection_expression)
    {
        return $this->escape_sql(
            $collection_expression->get_collection_name()
        );
    }

    /// Creating instances

    protected function new_sql_expression_builder()
    {
        return Create::object( SqlExpressionInFilterBuilder::class );
    }
}