<?php

namespace Haijin\Persistency\Statements\Expressions;

class Inner_Join_Expression extends Join_Expression
{
    /// Asking

    public function is_inner_join_expression()
    {
        return true;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_inner_join_expression( $this );
    }
}
