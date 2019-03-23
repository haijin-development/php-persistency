<?php

namespace Haijin\Persistency\Statements\Expressions;

class Full_Outer_Join_Expression extends Join_Expression
{
    /// Asking

    public function is_full_outer_join_expression()
    {
        return true;
    }

    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_full_outer_join_expression( $this );
    }
}
