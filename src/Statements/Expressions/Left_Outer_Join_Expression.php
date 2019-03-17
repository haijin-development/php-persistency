<?php

namespace Haijin\Persistency\Statements\Expressions;

class Left_Outer_Join_Expression extends Join_Expression
{
    /// Visiting

    public function accept_visitor($visitor)
    {
        return $visitor->accept_left_outer_join_expression( $this );
    }
}
