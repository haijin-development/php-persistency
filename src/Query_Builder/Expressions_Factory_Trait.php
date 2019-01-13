<?php

namespace Haijin\Persistency\Query_Builder;

use Haijin\Persistency\Query_Builder\Builders\Expression_Context;
use Haijin\Persistency\Query_Builder\Expressions\All_Fields_Expression;
use Haijin\Persistency\Query_Builder\Expressions\Field_Expression;
use Haijin\Persistency\Query_Builder\Expressions\Value_Expression;
use Haijin\Persistency\Query_Builder\Expressions\Named_Parameter_Expression;
use Haijin\Persistency\Query_Builder\Expressions\Alias_Expression;
use Haijin\Persistency\Query_Builder\Expressions\Function_Call_Expression;
use Haijin\Persistency\Query_Builder\Expressions\Binary_Operator_Expression;
use Haijin\Persistency\Query_Builder\Expressions\Brackets_Expression;
use Haijin\Tools\Dictionary;

/**
 * Trait with methods to create query expressions.
 */
trait Expressions_Factory_Trait
{
    /// Instance creation

    protected function new_query_expression()
    {
        return new Query_Expression(
            $this->context
        );
    }

    protected function new_collection_expression( $collection_name = null)
    {
        return new Collection_Expression(
            $this->context,
            $collection_name
        );
    }

    protected function new_proyection_expression()
    {
        return new Proyection_Expression(
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
        return new Join_Expression(
            $this->context,
            $from_collection,
            $to_collection
        );
    }

    protected function new_filter_expression($expression)
    {
        return new Filter_Expression(
            $this->context,
            $expression
        );
    }

    protected function new_order_by_expression()
    {
        return new Order_By_Expression(
            $this->context
        );
    }

    protected function new_pagination_expression()
    {
        return new Pagination_Expression(
            $this->context
        );
    }

    protected function new_all_fields_expression()
    {
        return new All_Fields_Expression(
            $this->context
        );
    }

    protected function new_field_expression($field_name)
    {
        return new Field_Expression(
            $this->context,
            $field_name
        );
    }

    protected function new_value_expression($value)
    {
        return new Value_Expression(
            $this->context,
            $value
        );
    }

    protected function new_named_parameter_expression($parameter_name)
    {
        return new Named_Parameter_Expression(
            $this->context,
            $parameter_name
        );
    }

    protected function new_alias_expression($alias, $aliased_expression)
    {
        return new Alias_Expression(
            $this->context,
            $alias,
            $aliased_expression
        );
    }

    protected function new_function_call_expression($function_name, $parameters)
    {
        return new Function_Call_Expression(
            $this->context,
            $function_name,
            $parameters
        );
    }

    protected function new_binary_operator_expression($operator_symbol, $parameter_1, $parameter_2)
    {
        return new Binary_Operator_Expression(
            $this->context,
            $operator_symbol,
            $parameter_1,
            $parameter_2
        );
    }

    protected function new_brackets_expression($expression = null)
    {
        return new Brackets_Expression(
            $this->context,
            $expression
        );
    }

    protected function new_expression_context($macro_expressions = null, $current_collection = null)
    {
        if( $macro_expressions === null ) {
            $macro_expressions = $this->new_macro_expressions_dictionary();
        }

        return new Expression_Context(
            $macro_expressions,
            $current_collection
        );
    }

    protected function new_macro_expressions_dictionary()
    {
        return new Dictionary();
    }
}