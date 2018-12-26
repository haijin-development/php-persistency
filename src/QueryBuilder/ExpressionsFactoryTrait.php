<?php

namespace Haijin\Persistency\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Builders\ExpressionContext;
use Haijin\Persistency\QueryBuilder\Expressions\AllFieldsExpression;
use Haijin\Persistency\QueryBuilder\Expressions\FieldExpression;
use Haijin\Persistency\QueryBuilder\Expressions\ValueExpression;
use Haijin\Persistency\QueryBuilder\Expressions\NamedParameterExpression;
use Haijin\Persistency\QueryBuilder\Expressions\AliasExpression;
use Haijin\Persistency\QueryBuilder\Expressions\FunctionCallExpression;
use Haijin\Persistency\QueryBuilder\Expressions\BinaryOperatorExpression;
use Haijin\Persistency\QueryBuilder\Expressions\BracketsExpression;
use Haijin\Tools\Dictionary;

/**
 * Trait with methods to create query expressions.
 */
trait ExpressionsFactoryTrait
{
    /// Instance creation

    protected function new_query_expression()
    {
        return new QueryExpression(
            $this->context
        );
    }

    protected function new_collection_expression( $collection_name = null)
    {
        return new CollectionExpression(
            $this->context,
            $collection_name
        );
    }

    protected function new_proyection_expression()
    {
        return new ProyectionExpression(
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
        return new JoinExpression(
            $this->context,
            $from_collection,
            $to_collection
        );
    }

    protected function new_filter_expression($expression)
    {
        return new FilterExpression(
            $this->context,
            $expression
        );
    }

    protected function new_order_by_expression()
    {
        return new OrderByExpression(
            $this->context
        );
    }

    protected function new_pagination_expression()
    {
        return new PaginationExpression(
            $this->context
        );
    }

    protected function new_all_fields_expression()
    {
        return new AllFieldsExpression(
            $this->context
        );
    }

    protected function new_field_expression($field_name)
    {
        return new FieldExpression(
            $this->context,
            $field_name
        );
    }

    protected function new_value_expression($value)
    {
        return new ValueExpression(
            $this->context,
            $value
        );
    }

    protected function new_named_parameter_expression($parameter_name)
    {
        return new NamedParameterExpression(
            $this->context,
            $parameter_name
        );
    }

    protected function new_alias_expression($alias, $aliased_expression)
    {
        return new AliasExpression(
            $this->context,
            $alias,
            $aliased_expression
        );
    }

    protected function new_function_call_expression($function_name, $parameters)
    {
        return new FunctionCallExpression(
            $this->context,
            $function_name,
            $parameters
        );
    }

    protected function new_binary_operator_expression($operator_symbol, $parameter_1, $parameter_2)
    {
        return new BinaryOperatorExpression(
            $this->context,
            $operator_symbol,
            $parameter_1,
            $parameter_2
        );
    }

    protected function new_brackets_expression($expression = null)
    {
        return new BracketsExpression(
            $this->context,
            $expression
        );
    }

    protected function new_expression_context($macro_expressions = null, $current_collection = null)
    {
        if( $macro_expressions === null ) {
            $macro_expressions = $this->new_macro_expressions_dictionary();
        }

        return new ExpressionContext(
            $macro_expressions,
            $current_collection
        );
    }

    protected function new_macro_expressions_dictionary()
    {
        return new Dictionary();
    }
}