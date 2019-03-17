<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Sql\Expression_Builders\Common_Expressions\Sql_Expression_In_Filter_Builder;

class Sql_Join_Builder extends Sql_Expression_Builder
{
    /// Visiting

    /**
     * Accepts an Inner_Join_Expression.
     */
    public function accept_inner_join_expression($join_expression)
    {
        return $this->build_sql_join_expression( 'join ', $join_expression );
    }

    /**
     * Accepts a Left_Outer_Join_Expression.
     */
    public function accept_left_outer_join_expression($join_expression)
    {
        return $this->build_sql_join_expression( 'left outer join ', $join_expression );
    }

    /**
     * Accepts a Right_Outer_Join_Expression.
     */
    public function accept_right_outer_join_expression($join_expression)
    {
        return $this->build_sql_join_expression( 'right outer join ', $join_expression );
    }

    /**
     * Accepts a Full_Outer_Join_Expression.
     */
    public function accept_full_outer_join_expression($join_expression)
    {
        return $this->build_sql_join_expression( 'full outer join ', $join_expression );
    }

    /**
     * Accepts an Inner_Join_Expression.
     */
    public function build_sql_join_expression($join_type, $join_expression)
    {
        return $join_type .
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
        return Create::object(
            Sql_Expression_In_Filter_Builder::class,
            $this->collected_parameters
        );
    }
}