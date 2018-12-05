<?php

namespace Haijin\Persistency\Sql\QueryBuilder;

use Haijin\Persistency\QueryBuilder\Visitors\QueryExpressionVisitor;
use Haijin\Tools\OrderedCollection;

/**
 * A builder of general expressions used in different query parts.
 */
class SqlExpressionBuilder extends QueryExpressionVisitor
{
    use SqlBuilderTrait;

    protected $collection;
    protected $collection_name;

    /// Initializing

    /**
     * Initializes $this instance.
     */
    public function __construct($collection, $use_fields_alias = true)
    {
        $this->collection = $collection;

        $this->collection_name = $this->escape(
            $collection->get_referenced_name()
        );

        $this->use_fields_alias = $use_fields_alias;
    }

    /// Visiting

    /**
     * Accepts an AllFieldsExpression.
     */
    public function accept_all_fields_expression($all_fields_expression)
    {
        return $this->collection_name . ".*";
    }

    /**
     * Accepts a FieldExpression.
     */
    public function accept_field_expression($field_expression)
    {
        $field = '';

        if( $field_expression->is_relative() ) {
            $field .= $this->collection_name;
            $field .= ".";
        }

        $field .= $this->escape( $field_expression->get_field_name() );

        return $field;
    }

    /**
     * Accepts a ValueExpression.
     */
    public function accept_value($value_expression)
    {
        return $this->value_to_sql( $value_expression->get_value() );
    }

    /**
     * Accepts a AliasExpression.
     */
    public function accept_alias_expression($alias_expression)
    {
        if( $this->use_fields_alias ) {
            return $alias_expression->get_alias();
        }

        $sql = $this->visit( $alias_expression->get_aliased_expression() );

        $sql .= " as ";

        $sql .= $this->escape( $alias_expression->get_alias() );

        return $sql;
    }

    /**
     * Accepts a FunctionCallExpression.
     */
    public function accept_function_call_expression($function_call_expression)
    {
        $function_name = $function_call_expression->get_function_name();
        if( in_array( $function_name, $this->unary_functions() ) ) {
            return $this->unary_function_sql( $function_name, $function_call_expression );
        }

        $sql = $this->escape( $function_call_expression->get_function_name() );
        $sql .= "(";

        $sql .= $this->expressions_list(
            OrderedCollection::with_all( $function_call_expression->get_parameters() )
        );

        $sql .= ")";

        return $sql;
    }

    protected function unary_functions()
    {
        return [
            "is_null",
            "is_not_null",
            "desc",
            "asc"
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
    }

    /**
     * Accepts a BinaryOperatorExpression.
     */
    public function accept_binary_operator_expression($binary_operator_expression)
    {
        $sql = $this->visit( $binary_operator_expression->get_parameter_1() );

        $sql .= " ";

        $sql .= $this->escape( $binary_operator_expression->get_operator_symbol() );

        $sql .= " ";

        $sql .= $this->visit( $binary_operator_expression->get_parameter_2() );

        return $sql;
    }

    /**
     * Accepts a BracketsExpression.
     */
    public function accept_brackets_expression($brackets_expression)
    {
        return "(" . $this->visit( $brackets_expression->get_expression() ) . ")";
    }
}