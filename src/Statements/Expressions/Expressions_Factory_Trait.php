<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statement_Compiler\Expression_Context;
use Haijin\Dictionary;
use Haijin\Ordered_Collection;

/**
 * Trait with methods to create query expressions.
 */
trait Expressions_Factory_Trait
{
    /// Instance creation

    public function new_collection_expression( $collection_name = null)
    {
        return Create::object( Collection_Expression::class, 
            $this->context,
            $collection_name
        );
    }

    public function new_proyection_expression()
    {
        return Create::object( Proyection_Expression::class, 
            $this->context
        );
    }

    public function new_proyection_expression_with_all($proyected_expressions)
    {
        $proyection = $this->new_proyection_expression();

        $proyection->add_all( $proyected_expressions );
    
        return $proyection;
    }

    public function new_inner_join_expression($from_collection, $to_collection)
    {
        return Create::object( Inner_Join_Expression::class, 
            $this->context,
            $from_collection,
            $to_collection
        );
    }

    public function new_left_outer_join_expression($from_collection, $to_collection)
    {
        return Create::object( Left_Outer_Join_Expression::class, 
            $this->context,
            $from_collection,
            $to_collection
        );
    }

    public function new_right_outer_join_expression($from_collection, $to_collection)
    {
        return Create::object( Right_Outer_Join_Expression::class, 
            $this->context,
            $from_collection,
            $to_collection
        );
    }

    public function new_full_outer_join_expression($from_collection, $to_collection)
    {
        return Create::object( Full_Outer_Join_Expression::class, 
            $this->context,
            $from_collection,
            $to_collection
        );
    }

    public function new_with_expression($from_collection, $joined_field_mapping)
    {
        return Create::object( With_Expression::class, 
            $this->context,
            $from_collection,
            $joined_field_mapping
        );
    }

    public function new_filter_expression($expression)
    {
        return Create::object( Filter_Expression::class, 
            $this->context,
            $expression
        );
    }

    public function new_group_by_expression_with_all($field_expressions)
    {
        $group_by = $this->new_group_by_expression();

        $group_by->add_all( $field_expressions );
    
        return $group_by;
    }

    public function new_group_by_expression()
    {
        return Create::object( Group_By_Expression::class, 
            $this->context
        );
    }

    public function new_having_expression($expression)
    {
        return Create::object( Having_Expression::class,
            $this->context,
            $expression
        );
    }

    public function new_order_by_expression()
    {
        return Create::object( Order_By_Expression::class, 
            $this->context
        );
    }

    public function new_pagination_expression()
    {
        return Create::object( Pagination_Expression::class, 
            $this->context
        );
    }

    public function new_all_fields_expression()
    {
        return Create::object( All_Fields_Expression::class,
            $this->context
        );
    }

    public function new_field_expression($field_name)
    {
        return Create::object( Field_Expression::class, 
            $this->context,
            $field_name
        );
    }

    public function new_value_expression($value)
    {
        return Create::object( Value_Expression::class, 
            $this->context,
            $value
        );
    }

    public function new_raw_expression($value)
    {
        return Create::object( Raw_Expression::class, 
            $this->context,
            $value
        );
    }

    public function new_named_parameter_expression($parameter_name)
    {
        return Create::object( Named_Parameter_Expression::class, 
            $this->context,
            $parameter_name
        );
    }

    public function new_alias_expression($alias, $aliased_expression)
    {
        return Create::object( Alias_Expression::class,
            $this->context,
            $alias,
            $aliased_expression
        );
    }

    public function new_function_call_expression($function_name, $parameters)
    {
        return Create::object( Function_Call_Expression::class, 
            $this->context,
            $function_name,
            $parameters
        );
    }

    public function new_binary_operator_expression($operator_symbol, $parameter_1, $parameter_2)
    {
        return Create::object( Binary_Operator_Expression::class, 
            $this->context,
            $operator_symbol,
            $parameter_1,
            $parameter_2
        );
    }

    public function new_brackets_expression($expression = null)
    {
        return Create::object( Brackets_Expression::class, 
            $this->context,
            $expression
        );
    }

    public function new_field_value_expression($field_name, $value_expression)
    {
        return Create::object( Field_Value_Expression::class, 
            $this->context,
            $field_name,
            $value_expression
        );
    }

    public function new_record_values_expression($record_attributes = [])
    {
        return Create::object( Record_Values_Expression::class, 
            $this->context,
            $record_attributes
        );
    }

    public function new_ignore_expression()
    {
        return Create::object( Ignore_Expression::class, 
            $this->context
        );
    }

    public function new_expression_context($macro_expressions = null, $current_collection = null)
    {
        if( $macro_expressions === null ) {
            $macro_expressions = $this->new_macro_expressions_dictionary();
        }

        return Create::object( Expression_Context::class, 
            $macro_expressions,
            $current_collection
        );
    }

    public function new_macro_expressions_dictionary()
    {
        return new Dictionary();
    }

    /// Helper methods

    /**
     * Converts values to expressions.
     */
    public function _values_to_expressions($values)
    {
        return Ordered_Collection::with_all( $values )
            ->collect( function($each_parameter) {
                return $this->_value_to_expression( $each_parameter );
            }, 
            $this )->to_array();
    }

    /**
     * Converts value to a ValueExpressions if it's not one.
     */
    public function _value_to_expression($value)
    {
        if( is_a( $value, Expression::class ) ) {

            return $value;

        } else {

            return $this->new_value_expression( $value );

        }
    }
}