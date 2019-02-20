<?php

namespace Haijin\Persistency\Statements\Expressions;

class Alias_Expression extends Expression
{
    protected $alias;
    protected $aliased_expression;

    /// Initializing

    public function __construct($expression_context, $alias, $aliased_expression)
    {
        parent::__construct( $expression_context );

        $this->alias = $alias;
        $this->aliased_expression = $aliased_expression;
    }

    /// Accessing

    public function get_alias()
    {
        return $this->alias;
    }

    public function get_aliased_expression()
    {
        return $this->aliased_expression;
    }

    public function get_referenced_name()
    {
        return $this->alias;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_alias_expression( $this );
    }
}
