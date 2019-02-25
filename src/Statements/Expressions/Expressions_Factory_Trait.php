<?php

namespace Haijin\Persistency\Statements\Expressions;

use Haijin\Instantiator\Create;
use Haijin\Persistency\Statement_Compiler\Expression_Context;
use Haijin\Dictionary;

/**
 * Trait with methods to create query expressions.
 */
trait Expressions_Factory_Trait
{
    /// Instance creation

    protected function new_collection_expression( $collection_name = null)
    {
        return Create::a( Collection_Expression::class )->with(
            $this->context,
            $collection_name
        );
    }

    protected function new_proyection_expression()
    {
        return Create::a( Proyection_Expression::class )->with(
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
        return Create::a( Join_Expression::class )->with(
            $this->context,
            $from_collection,
            $to_collection
        );
    }

    protected function new_filter_expression($expression)
    {
        return Create::a( Filter_Expression::class )->with(
            $this->context,
            $expression
        );
    }

    protected function new_order_by_expression()
    {
        return Create::a( Order_By_Expression::class )->with(
            $this->context
        );
    }

    protected function new_pagination_expression()
    {
        return Create::a( Pagination_Expression::class )->with(
            $this->context
        );
    }

    protected function new_count_expression()
    {
        return Create::a( Count_Expression::class )->with(
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
        return Create::a( Field_Expression::class )->with(
            $this->context,
            $field_name
        );
    }

    protected function new_value_expression($value)
    {
        return Create::a( Value_Expression::class )->with(
            $this->context,
            $value
        );
    }

    protected function new_named_parameter_expression($parameter_name)
    {
        return Create::a( Named_Parameter_Expression::class )->with(
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
        return Create::a( Function_Call_Expression::class )->with(
            $this->context,
            $function_name,
            $parameters
        );
    }

    protected function new_binary_operator_expression($operator_symbol, $parameter_1, $parameter_2)
    {
        return Create::a( Binary_Operator_Expression::class )->with(
            $this->context,
            $operator_symbol,
            $parameter_1,
            $parameter_2
        );
    }

    protected function new_brackets_expression($expression = null)
    {
        return Create::a( Brackets_Expression::class )->with(
            $this->context,
            $expression
        );
    }

    protected function new_field_value_expression($field_name, $value_expression)
    {
        return Create::a( Field_Value_Expression::class )->with(
            $this->context,
            $field_name,
            $value_expression
        );
    }

    protected function new_record_values_expression($record_attributes = [])
    {
        return Create::a( Record_Values_Expression::class )->with(
            $this->context,
            $record_attributes
        );
    }

    protected function new_expression_context($macro_expressions = null, $current_collection = null)
    {
        if( $macro_expressions === null ) {
            $macro_expressions = $this->new_macro_expressions_dictionary();
        }

        return Create::a( Expression_Context::class )->with(
            $macro_expressions,
            $current_collection
        );
    }

    protected function new_macro_expressions_dictionary()
    {
        return Create::a( Dictionary::class )->with();
    }
}