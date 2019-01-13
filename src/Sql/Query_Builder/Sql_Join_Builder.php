<?php

namespace Haijin\Persistency\Sql\Query_Builder;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Query_Builder\Visitors\Expressions\Join_Visitor;
use Haijin\Persistency\Sql\Query_Builder\Expression_Builders\Sql_Expression_In_Filter_Builder;

class Sql_Join_Builder extends Join_Visitor
{
    use Sql_Builder_Trait;

    /// Visiting

    /**
     * Accepts a Join_Expression.
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
     * Accepts a Alias_Expression. The alias at this DSL level is for the Collection_Expression.
     */
    public function accept_alias_expression($alias_expression)
    {
        $sql = $this->visit( $alias_expression->get_aliased_expression() );
        $sql .= " as ";
        $sql .= $this->escape_sql( $alias_expression->get_alias() );

        return $sql;
    }

    /**
     * Accepts a Collection_Expression.
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
        return Create::object( Sql_Expression_In_Filter_Builder::class );
    }
}