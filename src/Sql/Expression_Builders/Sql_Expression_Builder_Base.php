<?php

namespace Haijin\Persistency\Sql\Expression_Builders;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statements_Visitors\Expressions\Expression_Visitor;
use Haijin\Persistency\Sql\Sql_Builder_Trait;
use Haijin\Ordered_Collection;

/**
 * A builder of general expressions used in different query parts.
 */
class Sql_Expression_Builder_Base extends Expression_Visitor
{
    use Sql_Builder_Trait;

    /// Visiting

    /**
     * Accepts a Count_Expression.
     */
    public function accept_count_expression($count_expression)
    {
        return "count(*)";
    }

    /**
     * Accepts an All_Fields_Expression.
     */
    public function accept_all_fields_expression($all_fields_expression)
    {
        return $all_fields_expression->get_context_collection()
                    ->get_referenced_name() . ".*";
    }

    /**
     * Accepts a Field_Expression.
     */
    public function accept_field_expression($field_expression)
    {
        $field = '';

        if( $field_expression->is_relative() ) {
            $field .= $field_expression->get_context_collection()
                        ->get_referenced_name() . ".";
        }

        $field .= $this->escape_sql( $field_expression->get_field_name() );

        return $field;
    }

    /**
     * Accepts a Value_Expression.
     */
    public function accept_value_expression($value_expression)
    {
        return $this->value_to_sql( $value_expression->get_value() );
    }

    /**
     * Accepts a Alias_Expression.
     */
    public function accept_alias_expression($alias_expression)
    {
        return $alias_expression->get_alias();
    }

    /**
     * Accepts a Function_Call_Expression.
     */
    public function accept_function_call_expression($function_call_expression)
    {
        $function_name = $function_call_expression->get_function_name();
        if( in_array( $function_name, $this->special_sintax_functions() ) ) {
            return $this->unary_function_sql( $function_name, $function_call_expression );
        }

        $sql = $this->escape_sql( $function_call_expression->get_function_name() );
        $sql .= "(";

        $sql .= $this->expressions_list(
            Ordered_Collection::with_all( $function_call_expression->get_parameters() )
        );

        $sql .= ")";

        return $sql;
    }

    protected function special_sintax_functions()
    {
        return [
            "is_null",
            "is_not_null",
            "desc",
            "asc",
            "in"
        ];
    }

    protected function unary_function_sql($function_name, $function_call_expression)
    {
        if( $function_name == "is_null" ) {
            $receiver = $function_call_expression->get_parameters()[0];
            return $this->expression_sql_from( $receiver ) . " is null";
        }

        if( $function_name == "is_not_null" ) {
            $receiver = $function_call_expression->get_parameters()[0];
            return $this->expression_sql_from( $receiver ) . " is not null";
        }

        if( $function_name == "desc" ) {
            $receiver = $function_call_expression->get_parameters()[0];
            return $this->expression_sql_from( $receiver ) . " desc";
        }

        if( $function_name == "asc" ) {
            $receiver = $function_call_expression->get_parameters()[0];
            return $this->expression_sql_from( $receiver ) . " asc";
        }

        if( $function_name == "in" ) {
            $sql = $this->visit( $function_call_expression->get_parameters()[0] );

            $sql .= " in (";

            $ids = [];
            foreach( $function_call_expression->get_parameters()[ 1 ]->get_value() as $id ) {
                $ids[] = $this->value_to_sql( $id );
            }

            $sql .= join( ", ", $ids );
            $sql .= ")";

            return $sql;
        }
    }

    /**
     * Accepts a Binary_Operator_Expression.
     */
    public function accept_binary_operator_expression($binary_operator_expression)
    {
        $sql = $this->visit( $binary_operator_expression->get_parameter_1() );

        $sql .= " ";

        $sql .= $this->escape_sql( $binary_operator_expression->get_operator_symbol() );

        $sql .= " ";

        $sql .= $this->visit( $binary_operator_expression->get_parameter_2() );

        return $sql;
    }

    /**
     * Accepts a Brackets_Expression.
     */
    public function accept_brackets_expression($brackets_expression)
    {
        return "(" . $this->visit( $brackets_expression->get_expression() ) . ")";
    }

    protected function new_sql_expression_builder()
    {
        return Create::object( get_class( $this ) );
    }
}