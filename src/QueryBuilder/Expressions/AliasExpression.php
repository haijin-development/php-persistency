<?php

namespace Haijin\Persistency\QueryBuilder\Expressions;

use Haijin\Persistency\QueryBuilder\Expression;

class AliasExpression extends Expression
{
    protected $alias;
    protected $aliased_expression;

    /// Initializing

    public function __construct($macro_expressions, $alias, $aliased_expression)
    {
        parent::__construct( $macro_expressions );

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
