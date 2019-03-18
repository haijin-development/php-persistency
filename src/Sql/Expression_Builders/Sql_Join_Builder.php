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
            $this->field_sql_from( $join_expression->get_from_field() ) .
            " = " .
            $this->field_sql_from( $join_expression->get_to_field() );
    }

    /**
     * Accepts a With_Expression.
     */
    public function accept_with_expression($with_expression)
    {
        return $with_expression->build_join_expression_with( $this, $with_expression );
    }

    public function build_object_reference_to_sql($with_expression)
    {
        $reference = $with_expression->get_joined_field_mapping()->get_type();

        $from_field = $with_expression->get_joined_field_mapping()->get_field_name();

        $to_field = $reference->get_referenced_collection()->get_id_field();

        $with_expression->set_from_field(
            $with_expression->get_from_collection()->new_field_expression( $from_field )
        );

        $with_expression->set_to_field(
            $with_expression->new_field_expression( $to_field )
        );

        return $this->build_sql_join_expression( 'left outer join ', $with_expression );
    }

    public function build_object_reference_from_sql($with_expression)
    {
        $reference = $with_expression->get_joined_field_mapping()->get_type();

        $from_field = $with_expression->get_meta_model()->get_id_field();

        $to_field = $reference->get_id_field();

        $with_expression->set_from_field(
            $with_expression->get_from_collection()->new_field_expression( $from_field )
        );

        $with_expression->set_to_field(
            $with_expression->new_field_expression( $to_field )
        );

        return $this->build_sql_join_expression( 'left outer join ', $with_expression );
    }

    public function build_collection_from_sql($with_expression)
    {
        return $this->build_object_reference_from_sql( $with_expression );
    }

    public function build_collection_through_sql($with_expression)
    {
        $reference = $with_expression->get_joined_field_mapping()->get_type();

        $left_collection = $with_expression->get_from_collection();

        $middle_table_name = $reference->get_middle_table();

        $right_collection = $reference->get_referenced_collection();

        $from_field = $this->field_sql_from(
            $with_expression->get_from_collection()->new_field_expression(
                $with_expression->get_meta_model()->get_id_field()
            )
        );

        $to_field = $this->field_sql_from(
            $with_expression->new_field_expression(
                $reference->get_referenced_collection()->get_id_field()
            )
        );

        return 
            "left outer join {$middle_table_name} " .
            "on {$from_field} = {$middle_table_name}.{$reference->get_left_id_field()} " .
            "left outer join {$right_collection->get_collection_name()} " .
            "on {$middle_table_name}.{$reference->get_right_id_field()} = {$to_field}";
    }

    protected function collection_sql_from($join_expression)
    {
        return $this->visit( $join_expression->get_to_collection() );
    }

    protected function field_sql_from($field_expression)
    {
        return $this->new_sql_expression_builder()
            ->visit( $field_expression );
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