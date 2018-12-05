<?php

namespace Haijin\Persistency\QueryBuilder\Expressions;

/**
 * A trait to add allow expressions to perform unary functions like
 *      $query ->value( 1 ) ->is_not_null()
 */
trait ExpressionTrait
{
    /**
     * Wrapps the last proyected field with an AliasExpression.
     */
    public function as($alias)
    {
        return $this->new_alias_expression( $alias, $this );
    }

    /**
     * Returns a BinaryOperatorExpression with $this object as its first argument.
     * The second argument is not defined, will be when the BinaryOperatorExpression
     * receives a new message.
     */
    public function op($operator_symbol)
    {
        return $this->new_binary_operator_expression( $operator_symbol, $this, null );
    }

    /**
     * Shurtcut for $this->op( "and" ).
     */
    public function and()
    {
        return $this->op( "and" );
    }

    /**
     * Shurtcut for $this->op( "or" ).
     */
    public function or()
    {
        return $this->op( "or" );
    }

    /**
     * Wraps a query Expression with brackets.
     */
    public function brackets($expression)
    {
        return $this->new_brackets_expression( $expression );
    }

    public function __call($function_name, $parameters)
    {
        return $this->new_function_call_expression( $function_name, [ $this ] );
    }
}