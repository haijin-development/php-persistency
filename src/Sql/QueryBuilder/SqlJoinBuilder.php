<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\QueryExpressionVisitor;

class SqlJoinBuilder extends QueryExpressionVisitor
{
    use SqlBuilderTrait;

    protected $collection;

    public function __construct($collection_expression)
    {
        $this->collection = $collection_expression;
    }

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
        return $this->escape(
            $join_expression->get_collection()->get_collection_name()
        );
    }

    protected function from_field_sql_from($join_expression)
    {
        return $this->new_sql_expression_builder( $this->collection )
            ->visit( $join_expression->get_from_field() );
    }

    protected function to_field_sql_from($join_expression)
    {
        return $this->new_sql_expression_builder( $join_expression->get_collection() )
            ->visit( $join_expression->get_to_field() );
    }

    protected function new_sql_expression_builder($collection)
    {
        return new SqlExpressionBuilder($collection);
    }
}