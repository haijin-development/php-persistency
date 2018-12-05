<?php

namespace Haijin\Persistency\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Expressions\AllFieldsExpression;
use Haijin\Persistency\QueryBuilder\Expressions\FieldExpression;
use Haijin\Persistency\QueryBuilder\Expressions\ValueExpression;
use Haijin\Persistency\QueryBuilder\Expressions\AliasExpression;
use Haijin\Persistency\QueryBuilder\Expressions\FunctionCallExpression;
use Haijin\Persistency\QueryBuilder\Expressions\BinaryOperatorExpression;
use Haijin\Persistency\QueryBuilder\Expressions\BracketsExpression;

/**
 * Trait with methods to create query expressions.
 */
trait ExpressionsFactoryTrait
{
    /// Instance creation

    protected function new_query_expression()
    {
        return new QueryExpression(
            $this->macro_expressions
        );
    }

    protected function new_collection_expression( $collection_name = null)
    {
        return new CollectionExpression(
            $this->macro_expressions,
            $collection_name
        );
    }

    protected function new_proyection_expression()
    {
        return new ProyectionExpression(
            $this->macro_expressions
        );
    }

    protected function new_join_expression($joined_collection_name)
    {
        return new JoinExpression(
            $this->macro_expressions,
            $joined_collection_name
        );
    }

    protected function new_filter_expression($expression)
    {
        return new FilterExpression(
            $this->macro_expressions,
            $expression
        );
    }

    protected function new_order_by_expression()
    {
        return new OrderByExpression(
            $this->macro_expressions
        );
    }

    protected function new_pagination_expression()
    {
        return new PaginationExpression(
            $this->macro_expressions
        );
    }

    protected function new_all_fields_expression()
    {
        return new AllFieldsExpression(
            $this->macro_expressions
        );
    }

    protected function new_field_expression($field_name)
    {
        return new FieldExpression(
            $this->macro_expressions,
            $field_name
        );
    }

    protected function new_value_expression($value)
    {
        return new ValueExpression(
            $this->macro_expressions,
            $value
        );
    }

    protected function new_alias_expression($alias, $aliased_expression)
    {
        return new AliasExpression(
            $this->macro_expressions,
            $alias,
            $aliased_expression
        );
    }

    protected function new_function_call_expression($function_name, $parameters)
    {
        return new FunctionCallExpression(
            $this->macro_expressions,
            $function_name,
            $parameters
        );
    }

    protected function new_binary_operator_expression($operator_symbol, $parameter_1, $parameter_2)
    {
        return new BinaryOperatorExpression(
            $this->macro_expressions,
            $operator_symbol,
            $parameter_1,
            $parameter_2
        );
    }

    protected function new_brackets_expression($expression = null)
    {
        return new BracketsExpression(
            $this->macro_expressions,
            $expression
        );
    }
}