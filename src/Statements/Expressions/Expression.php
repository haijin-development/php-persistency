<?php

namespace Haijin\Persistency\Statements\Expressions;

/**
 * Base class for query expressions.
 */
abstract class Expression
{
    use Expressions_Factory_Trait;

    protected $context;

    /// Initializing

    public function __construct($expression_context)
    {
        $this->context = $expression_context;
    }

    /// Macro expressions

    public function get_context()
    {
        return $this->context;
    }

    public function set_context($expression_context)
    {
        $this->context = $expression_context;
    }

    public function get_macros_dictionary()
    {
        return $this->get_context()->get_macros_dictionary();
    }

    public function get_context_collection()
    {
        return $this->get_context()->get_current_collection();
    }

    /// Visiting

    abstract public function accept_visitor($visitor);

    /// Asking

    public function is_query_statement()
    {
        return false;
    }

    public function is_create_statement()
    {
        return false;
    }

    public function is_update_statement()
    {
        return false;
    }

    public function is_delete_statement()
    {
        return false;
    }

    public function is_alias_expression()
    {
        return false;
    }

    public function is_all_fields_expression()
    {
        return false;
    }

    public function is_binary_operator_expression()
    {
        return false;
    }

    public function is_brackets_expression()
    {
        return false;
    }

    public function is_collection_expression()
    {
        return false;
    }

    public function is_field_value_expression()
    {
        return false;
    }

    public function is_filter_expression()
    {
        return false;
    }

    public function is_full_outer_join_expression()
    {
        return false;
    }

    public function is_group_by_expression()
    {
        return false;
    }

    public function is_having_expression()
    {
        return false;
    }

    public function is_inner_join_expression()
    {
        return false;
    }

    public function is_left_outer_join_expression()
    {
        return false;
    }

    public function is_named_parameter_expression()
    {
        return false;
    }

    public function is_order_by_expression()
    {
        return false;
    }

    public function is_pagination_expression()
    {
        return false;
    }

    public function is_proyection_expression()
    {
        return false;
    }

    public function is_field_expression()
    {
        return false;
    }

    public function is_value_expression()
    {
        return false;
    }

    public function is_function_call_expression()
    {
        return false;
    }

    public function is_ignore_expression()
    {
        return false;
    }

    public function is_raw_expression()
    {
        return false;
    }

    public function is_record_values_expression()
    {
        return false;
    }

    public function is_right_outer_join_expression()
    {
        return false;
    }

    public function is_with_expression()
    {
        return false;
    }
}