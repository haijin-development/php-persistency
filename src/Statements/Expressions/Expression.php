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
}