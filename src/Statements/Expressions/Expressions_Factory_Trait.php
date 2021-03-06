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

    protected function new_collection_expression( $collection_name = null)
    {
        return Create::object( Collection_Expression::class, 
            $this->context,
            $collection_name
        );
    }

    protected function new_proyection_expression()
    {
        return Create::object( Proyection_Expression::class, 
            $this->context
        );
    }

    protected function new_proyection_expression_with_all($proyected_expressions)
    {
        $proyection = $this->new_proyection_expression();

        $proyection->add_all( $proyected_expressions );
    
        return $proyection;
    }

    protected function new_join_expression($from_collection, $to_collection)
    {
        return Create::object( Join_Expression::class, 
            $this->context,
            $from_collection,
            $to_collection
        );
    }

    protected function new_filter_expression($expression)
    {
        return Create::object( Filter_Expression::class, 
            $this->context,
            $expression
        );
    }

    protected function new_group_by_expression_with_all($field_expressions)
    {
        $group_by = $this->new_group_by_expression();

        $group_by->add_all( $field_expressions );
    
        return $group_by;
    }

    protected function new_group_by_expression()
    {
        return Create::object( Group_By_Expression::class, 
            $this->context
        );
    }

    protected function new_order_by_expression()
    {
        return Create::object( Order_By_Expression::class, 
            $this->context
        );
    }

    protected function new_pagination_expression()
    {
        return Create::object( Pagination_Expression::class, 
            $this->context
        );
    }

    protected function new_count_expression()
    {
        return Create::object( Count_Expression::class, 
            $this->context
        );
    }

    protected function new_all_fields_expression()
    {
        return Create::an( All_Fields_Expression::class )->with(
            $this->context
        );
    }

    protected function new_field_expression($field_name)
    {
        return Create::object( Field_Expression::class, 
            $this->context,
            $field_name
        );
    }

    protected function new_value_expression($value)
    {
        return Create::object( Value_Expression::class, 
            $this->context,
            $value
        );
    }

    protected function new_named_parameter_expression($parameter_name)
    {
        return Create::object( Named_Parameter_Expression::class, 
            $this->context,
            $parameter_name
        );
    }

    protected function new_alias_expression($alias, $aliased_expression)
    {
        return Create::an( Alias_Expression::class )->with(
            $this->context,
            $alias,
            $aliased_expression
        );
    }

    protected function new_function_call_expression($function_name, $parameters)
    {
        return Create::object( Function_Call_Expression::class, 
            $this->context,
            $function_name,
            $parameters
        );
    }

    protected function new_binary_operator_expression($operator_symbol, $parameter_1, $parameter_2)
    {
        return Create::object( Binary_Operator_Expression::class, 
            $this->context,
            $operator_symbol,
            $parameter_1,
            $parameter_2
        );
    }

    protected function new_brackets_expression($expression = null)
    {
        return Create::object( Brackets_Expression::class, 
            $this->context,
            $expression
        );
    }

    protected function new_field_value_expression($field_name, $value_expression)
    {
        return Create::object( Field_Value_Expression::class, 
            $this->context,
            $field_name,
            $value_expression
        );
    }

    protected function new_record_values_expression($record_attributes = [])
    {
        return Create::object( Record_Values_Expression::class, 
            $this->context,
            $record_attributes
        );
    }

    protected function new_ignore_expression()
    {
        return Create::object( Ignore_Expression::class, 
            $this->context
        );
    }

    protected function new_expression_context($macro_expressions = null, $current_collection = null)
    {
        if( $macro_expressions === null ) {
            $macro_expressions = $this->new_macro_expressions_dictionary();
        }

        return Create::object( Expression_Context::class, 
            $macro_expressions,
            $current_collection
        );
    }

    protected function new_macro_expressions_dictionary()
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